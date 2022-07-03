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
$db_con = new mysqli('localhost', 'dbuser', 'dbpass', 'dbname');

if($db_con->connect_errno > 0) die('Unable to connect to database [' . $db_con->connect_error . ']');


  mysqli_set_charset($db_con, 'utf8');

function db($sql, $debug = false)
{
global $db_con;

    if ($debug == true)
        echo '<BR />debug it : ' . $sql . '<BR />';
    if (strpos(strtoupper($sql), 'SELECT') === false)
       { $result = $db_con->query($sql);}
    else {
if(!$result = $db_con->query($sql)){
    die('There was an error running the query [' . $db_con->error . ']<br />MySql error. debug it : '.$sql);
}
        $line = false;
        if (strpos(strtoupper($sql), 'LIMIT 1;') !== false) $line = true;
        if (!$line)
           while($row = $result->fetch_assoc()){
			   if(count($row)==1) foreach($row AS $temp_val) $data[] = $temp_val;
			   else $data[] = $row;
            } else
                { $row = $result->fetch_assoc();
                   if(!empty($row)) if(count($row)==1) { foreach($row AS $data); }
                    else $data = $row;}
}


    if (!empty($data)) return $data;
    else return false;
}
function db_insert($table, $fields, $memberalues)
{

    if (is_array($fields)) {
        $count = count($fields);
		$fields_query = ''; $memberalues_query = '';
        for ($i = 0; $i < $count; $i++) {
            $field = '`' . $fields[$i] . '`';
            $memberalue = escape_string($memberalues[$i]);
            $fields_query .= $field;
            if (is_numeric($field))
                $memberalues_query .= $memberalue;
            else
                $memberalues_query .= "'" . $memberalue . "'";
            if ($i != $count - 1) {
                $fields_query .= ',';
                $memberalues_query .= ',';
            }
        }
    } else {
        $field_query = $field;
        $memberalue_query = escape_string($memberalues);
    }
    $sql = "INSERT into `$table` ($fields_query) VALUES ($memberalues_query)";
    db($sql);
}
function db_update($table, $fields, $memberalues, $where = '')
{
$part_query = '';
    if (is_array($fields)) {
        $count = count($fields) - 1;
        for ($i = 0; $i <= $count; $i++) {
            $field        = $fields[$i];
            $memberalue        = escape_string($memberalues[$i]);
            $fields_query = $field;
            $part_query .= '`' . $field . '`';
            if (is_numeric($field))
                $part_query .= ' = ' . $memberalue;
            else
                $part_query .= " ='" . $memberalue . "'";
            if ($count != $i)
                $part_query .= ' ,';
        }
    } else {
        $part_query = '`' . $fields . '`';
        if (is_numeric($memberalues))
            $part_query .= ' = ' . $memberalues;
        else
            $part_query .= " ='" . escape_string($memberalues) . "'";
    }
    $sql = "UPDATE `$table` SET $part_query $where";
    db($sql);
}
function db_delete($table, $field, $is)
{

    $sql = "DELETE FROM $table where $field = '" . escape_string($is) . "'";
    db($sql);
}
function db_select($table, $fields = "*", $arg1 = '', $arg2 = '', $arg3 = '')
{

    if (!is_array($fields))
        if ($fields == '*')
            $fields_query = $fields;
        else
            $fields_query = '`' . $fields . '`';
    else {
        $count = count($fields) - 1;
        for ($i = 0; $i <= $count; $i++) {
            $fields_query .= '`' . $fields[$i] . '`';
            if ($count != $i)
                $fields_query .= ',';
        }
    }
    $sql = "SELECT $fields_query FROM `$table` $arg1 $arg2 $arg3";
    return db($sql);
}
function db_select_one($table, $fields = "*", $arg1 = '', $arg2 = '')
{

    if (!is_array($fields))
        if ($fields == '*')
            $fields_query = $fields;
        else
            $fields_query = '`' . $fields . '`';
    else {
        $count = count($fields) - 1;
        for ($i = 0; $i <= $count; $i++) {
            $fields_query .= '`' . $fields[$i] . '`';
            if ($count != $i)
                $fields_query .= ',';
        }
    }
    $sql = "SELECT $fields_query FROM `$table` $arg1 $arg2 LIMIT 1 ";
    return db($sql);
}


function db_rows_num($sql)
{
    global $db_con;


if ($result=mysqli_query($db_con,$sql))
  {

  return mysqli_num_rows($result);

  }

}

function escape_string($memberalue)
{
global $db_con;
return $db_con->real_escape_string($memberalue);
}

function fields($sql)
{

global $db_con;

if ($result=mysqli_query($db_con,$sql))
  {
  $finfo=mysqli_fetch_fields($result);

foreach ($finfo as $memberal) $fields[] =  $memberal->name;


return $fields;
}
}

function datacheck($data)
{
global $db_con;
return array_map(array($db_con, 'real_escape_string'), $data);
}


function insert($table,$memberalues)
{
$query = "SELECT * FROM `$table` LIMIT 1;";
$fields = fields($query);
foreach($fields AS $field)
{
if(isset($memberalues[$field])) {$f[] = $field; $member[] = $memberalues[$field];}
}
db_insert($table,$f,$member);
}

function update($table,$memberalues,$where)
{
$query = "SELECT * FROM `$table` LIMIT 1;";
$fields = fields($query);
foreach($fields AS $field)
{
if(isset($memberalues[$field])) {$f[] = $field; $member[] = $memberalues[$field];}
}
db_update($table,$f,$member,$where);

}
?>
