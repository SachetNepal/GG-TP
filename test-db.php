<?php
require_once "db.php";

$sql = "SELECT PRODUCT_ID, PRODUCT_NAME FROM PRODUCT FETCH FIRST 10 ROWS ONLY";
$stid = oci_parse($conn, $sql);
oci_execute($stid);

echo "<h2>Products from Oracle</h2>";

while ($row = oci_fetch_assoc($stid)) {
    echo $row['PRODUCT_ID'] . " - " . $row['PRODUCT_NAME'] . "<br>";
}

oci_free_statement($stid);
oci_close($conn);
?>