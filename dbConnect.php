<?php
 function makeSQLI()
 {
 	return new mysqli("projecttrans.db.7260466.hostedresource.com","projecttrans","C0nnect", "projecttrans");
 }
 function makeLocalSQLI()
 {
 	return new mysqli("localhost","root","", "projecttrans");
 }
?>