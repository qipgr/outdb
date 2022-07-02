# outdb
outdb small php sql framework

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


1. SAMPLE : 



1.1 SELECT many rows :

1.1.1 - All the table
$data = db("SELECT * FROM `somedata` ");

result : $data[$i]['id']

1.1.2 - one field of all the table

$data = db("SELECT `id` FROM `somedata` WHERE `user` LIKE `Giorgos'");

result : $data[$i]



1.2.0 SELECT one row (LIMIT 1;)

$data = db("SELECT * FROM `somedata` WHERE `id`=114 LIMIT 1;");

result : $data['id']

1.2.1 SELECT one field one row (LIMIT 1;)

$data = db("SELECT `title` FROM `somedata` WHERE `id`=114 LIMIT 1;");

result : $data


1.3.0 INSERT

$table = 'somedata'; $input['id'] = 3; $input['article'] = 'some blah blah';

insert($table,$input);

1.4.0 UPDATE

$table = 'somedata'; $input['id'] = 4; $input['article'] = 'some blah blah AFTO'; $where = "WHERE `week` = 'Monday'";
update($table,$input,$where);

1.5.0 DELETE
db("DELETE FROM `somedata` WHERE `id` LIKE 1;");

Creator : George Katsoupakis
Quality Intrnet Productions qip.gr / outdb.com
