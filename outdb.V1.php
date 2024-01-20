<?php
/*
MIT License
Copyright (c) 2022 qipgr
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

$db_con = new mysqli('host', 'username', 'password', 'dbname');

if ($db_con->connect_errno > 0) {
    throw new Exception('Unable to connect to database [' . $db_con->connect_error . ']');
}

mysqli_set_charset($db_con, 'utf8mb4');

function db(string $sql, bool $debug = false)
{
    global $db_con;

    if ($debug) {
        echo '<BR />debug it : ' . $sql . '<BR />';
    }

    $result = $db_con->query($sql);

    if ($result === false) {
        throw new Exception('There was an error running the query [' . $db_con->error . ']<br />MySql error. debug it : ' . $sql);
    }

    $data = [];

    if (strpos(strtoupper($sql), 'SELECT') !== false) {
        $line = false;

        if (strpos(strtoupper($sql), 'LIMIT 1;') !== false) {
            $line = true;
        }

        if (!$line) {
            while ($row = $result->fetch_assoc()) {
                $data[] = (count($row) == 1) ? reset($row) : $row;
            }
        } else {
            $row = $result->fetch_assoc();
            $data = (!empty($row)) ? ((count($row) == 1) ? reset($row) : $row) : null;
        }
    }

    return (!empty($data)) ? $data : false;
}

function db_insert(string $table, array $fields, array $values)
{
    $fields_query = implode(', ', array_map(function ($field) {
        return "`$field`";
    }, $fields));

    $values_query = implode(', ', array_map(function ($value) {
        return is_numeric($value) ? $value : "'" . escape_string($value) . "'";
    }, $values));

    $sql = "INSERT INTO `$table` ($fields_query) VALUES ($values_query)";
    db($sql);
}

function db_update(string $table, array $fields, array $values, string $where = '')
{
    $set_clause = implode(', ', array_map(function ($field, $value) {
        $value = is_numeric($value) ? $value : "'" . escape_string($value) . "'";
        return "`$field` = $value";
    }, $fields, $values));

    $sql = "UPDATE `$table` SET $set_clause $where";
    db($sql);
}

function db_rows_num(string $sql)
{
    global $db_con;

    $result = mysqli_query($db_con, $sql);

    if ($result) {
        return mysqli_num_rows($result);
    }
}

function escape_string(string $value)
{
    global $db_con;
    return $db_con->real_escape_string($value);
}

function fields(string $sql)
{
    global $db_con;

    $result = mysqli_query($db_con, $sql);

    if ($result) {
        $finfo = mysqli_fetch_fields($result);
        $fields = array_map(function ($field) {
            return $field->name;
        }, $finfo);

        return $fields;
    }
}

function insert(string $table, array $values)
{
    $query = "SELECT * FROM `$table` LIMIT 1;";
    $fields = fields($query);
    $fieldsToInsert = [];
    $valuesToInsert = [];

    foreach ($fields as $field) {
        if (isset($values[$field])) {
            $fieldsToInsert[] = $field;
            $valuesToInsert[] = $values[$field];
        }
    }

    db_insert($table, $fieldsToInsert, $valuesToInsert);
}

function update(string $table, array $values, string $where)
{
    $query = "SELECT * FROM `$table` LIMIT 1;";
    $fields = fields($query);
    $fieldsToUpdate = [];
    $valuesToUpdate = [];

    foreach ($fields as $field) {
        if (isset($values[$field])) {
            $fieldsToUpdate[] = $field;
            $valuesToUpdate[] = $values[$field];
        }
    }

    db_update($table, $fieldsToUpdate, $valuesToUpdate, $where);
}
?>
