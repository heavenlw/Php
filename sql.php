<?php
class mysql {
private $db_host; 
private $db_user; 
private $db_pwd; 
private $db_database; 
private $conn; 
private $result; 
private $sql;
private $row;
private $coding; 
private $bulletin = true; 
private $show_error = false;
private $is_error = false; 
/*????*/
public function __construct($db_host, $db_user, $db_pwd, $db_database, $conn, $coding) {
$this->db_host = $db_host;
$this->db_user = $db_user;
$this->db_pwd = $db_pwd;
$this->db_database = $db_database;
$this->conn = $conn;
$this->coding = $coding;
$this->connect();
}
/*?????*/
public function connect() {
if ($this->conn == "pconn") {
//????
$this->conn = mysql_pconnect($this->db_host, $this->db_user, $this->db_pwd);
} else {
//????
$this->conn = mysql_connect($this->db_host, $this->db_user, $this->db_pwd);
}
if (!mysql_select_db($this->db_database, $this->conn)) {
if ($this->show_error) {
$this->show_error("??????:", $this->db_database);
}
}
mysql_query("SET NAMES $this->coding");
}
/*???????,??????????????sql??*/
public function query($sql) {
if ($sql == "") {
$this->show_error("SQL????:", "SQL??????");
}
$this->sql = $sql;
$result = mysql_query($this->sql, $this->conn);
if (!$result) {
//?????,sql????????????
if ($this->show_error) {
$this->show_error("??SQL??:", $this->sql);
}
} else {
$this->result = $result;
}
return $this->result;
}
/*?????????*/
public function create_database($database_name) {
$database = $database_name;
$sqlDatabase = 'create database ' . $database;
$this->query($sqlDatabase);
}
/*??????????*/
//??????????????,???????
public function show_databases() {
$this->query("show databases");
echo "?????:" . $amount = $this->db_num_rows($result);
echo "<br />";
$i = 1;
while ($row = $this->fetch_array($result)) {
echo "$i $row[Database]";
echo "<br />";
$i++;
}
}
//????????????????
public function databases() {
$rsPtr = mysql_list_dbs($this->conn);
$i = 0;
$cnt = mysql_num_rows($rsPtr);
while ($i < $cnt) {
$rs[] = mysql_db_name($rsPtr, $i);
$i++;
}
return $result;
}
/*??????????*/
public function show_tables($database_name) {
$this->query("show tables");
echo "?????:" . $amount = $this->db_num_rows($result);
echo "<br />";
$i = 1;
while ($row = $this->fetch_array($result)) {
$columnName = "Tables_in_" . $database_name;
echo "$i $row[$columnName]";
echo "<br />";
$i++;
}
}
/*
mysql_fetch_row() array $row[0],$row[1],$row[2]
mysql_fetch_array() array $row[0] ? $row[id]
mysql_fetch_assoc() array ?$row->content ???????
mysql_fetch_object() object ?$row[id],$row[content] ???????
*/
/*??????*/
public function mysql_result_li() {
return mysql_result($str);
}
/*?????,????-?????,??$row['content'] */
public function fetch_array($resultt="") {
if($resultt<>""){
return mysql_fetch_array($resultt);
}else{
return mysql_fetch_array($this->result);
}
}
//??????,??$row['???']
public function fetch_assoc() {
return mysql_fetch_assoc($this->result);
}
//????????,??$row[0],$row[1],$row[2]
public function fetch_row() {
return mysql_fetch_row($this->result);
}
//??????,??$row->content
public function fetch_Object() {
return mysql_fetch_object($this->result);
}
//????select
public function findall($table) {
$this->query("SELECT * FROM $table");
}
//????select
public function select($table, $columnName = "*", $condition = '', $debug = '') {
$condition = $condition ? ' Where ' . $condition : NULL;
if ($debug) {
echo "SELECT $columnName FROM $table $condition";
} else {
$this->query("SELECT $columnName FROM $table $condition");
}
}
//????del
public function delete($table, $condition, $url = '') {
if ($this->query("DELETE FROM $table WHERE $condition")) {
if (!empty ($url))
$this->Get_admin_msg($url, '????!');
}
}
//????insert
public function insert($table, $columnName, $value, $url = '') {
if ($this->query("INSERT INTO $table ($columnName) VALUES ($value)")) {
if (!empty ($url))
$this->Get_admin_msg($url, '????!');
}
}
//????update
public function update($table, $mod_content, $condition, $url = '') {
//echo "UPDATE $table SET $mod_content WHERE $condition"; exit();
if ($this->query("UPDATE $table SET $mod_content WHERE $condition")) {
if (!empty ($url))
$this->Get_admin_msg($url);
}
}
/*????? INSERT ????? ID*/
public function insert_id() {
return mysql_insert_id();
}
//???????????
public function db_data_seek($id) {
if ($id > 0) {
$id = $id -1;
}
if (!@ mysql_data_seek($this->result, $id)) {
$this->show_error("SQL????:", "???????");
}
return $this->result;
}
// ??select???????????
public function db_num_rows() {
if ($this->result == null) {
if ($this->show_error) {
$this->show_error("SQL????", "????,??????!");
}
} else {
return mysql_num_rows($this->result);
}
}
// ??insert,update,delete??????????
public function db_affected_rows() {
return mysql_affected_rows();
}
//????sql??
public function show_error($message = "", $sql = "") {
if (!$sql) {
echo "<font color='red'>" . $message . "</font>";
echo "<br />";
} else {
echo "<fieldset>";
echo "<legend>??????:</legend><br />";
echo "<div style='font-size:14px; clear:both; font-family:Verdana, Arial, Helvetica, sans-serif;'>";
echo "<div style='height:20px; background:#000000; border:1px #000000 solid'>";
echo "<font color='white'>???:12142</font>";
echo "</div><br />";
echo "????:" . mysql_error() . "<br /><br />";
echo "<div style='height:20px; background:#FF0000; border:1px #FF0000 solid'>";
echo "<font color='white'>" . $message . "</font>";
echo "</div>";
echo "<font color='red'><pre>" . $sql . "</pre></font>";
$ip = $this->getip();
if ($this->bulletin) {
$time = date("Y-m-d H:i:s");
$message = $message . "\r\n$this->sql" . "\r\n??IP:$ip" . "\r\n?? :$time" . "\r\n\r\n";
$server_date = date("Y-m-d");
$filename = $server_date . ".txt";
$file_path = "error/" . $filename;
$error_content = $message;
//$error_content="??????,?????";
$file = "error"; //????????
//?????
if (!file_exists($file)) {
if (!mkdir($file, 0777)) {
//??? mode ? 0777,???????????
die("upload files directory does not exist and creation failed");
}
}
//??txt????
if (!file_exists($file_path)) {
//echo "??????";
fopen($file_path, "w+");
//?????????????
if (is_writable($file_path)) {
//????????$filename,????????????
if (!$handle = fopen($file_path, 'a')) {
echo "?????? $filename";
exit;
}
//?$somecontent????????????
if (!fwrite($handle, $error_content)) {
echo "??????? $filename";
exit;
}
//echo "?? $filename ????";
echo "——???????!";
//????
fclose($handle);
} else {
echo "?? $filename ???";
}
} else {
//?????????????
if (is_writable($file_path)) {
//????????$filename,????????????
if (!$handle = fopen($file_path, 'a')) {
echo "?????? $filename";
exit;
}
//?$somecontent????????????
if (!fwrite($handle, $error_content)) {
echo "??????? $filename";
exit;
}
//echo "?? $filename ????";
echo "——???????!";
//????
fclose($handle);
} else {
echo "?? $filename ???";
}
}
}
echo "<br />";
if ($this->is_error) {
exit;
}
}
echo "</div>";
echo "</fieldset>";
echo "<br />";
}
//?????
public function free() {
@ mysql_free_result($this->result);
}
//?????
public function select_db($db_database) {
return mysql_select_db($db_database);
}
//??????
public function num_fields($table_name) {
//return mysql_num_fields($this->result);
$this->query("select * from $table_name");
echo "<br />";
echo "???:" . $total = mysql_num_fields($this->result);
echo "<pre>";
for ($i = 0; $i < $total; $i++) {
print_r(mysql_fetch_field($this->result, $i));
}
echo "</pre>";
echo "<br />";
}
//?? MySQL ?????
public function mysql_server($num = '') {
switch ($num) {
case 1 :
return mysql_get_server_info(); //MySQL ?????
break;
case 2 :
return mysql_get_host_info(); //?? MySQL ????
break;
case 3 :
return mysql_get_client_info(); //?? MySQL ?????
break;
case 4 :
return mysql_get_proto_info(); //?? MySQL ????
break;
default :
return mysql_get_client_info(); //????mysql????
}
}
//????,???????,??????
public function __destruct() {
if (!empty ($this->result)) {
$this->free();
}
mysql_close($this->conn);
} //function __destruct();
/*????????IP??*/
function getip() {
if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
$ip = getenv("HTTP_CLIENT_IP");
} else
if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
$ip = getenv("HTTP_X_FORWARDED_FOR");
} else
if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
$ip = getenv("REMOTE_ADDR");
} else
if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
$ip = $_SERVER['REMOTE_ADDR'];
} else {
$ip = "unknown";
}
return ($ip);
}
function inject_check($sql_str) { //????
$check = eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str);
if ($check) {
echo "????????!";
exit ();
} else {
return $sql_str;
}
}
function checkurl() { //????
if (preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) !== preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) {
header("Location: awol");
exit();
}
}
}
?>