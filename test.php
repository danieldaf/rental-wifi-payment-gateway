<?php

include_once 'config/init.php';

//$paymongo = new Paymongo();
//$paymongo->expireCheckout('cs_FyNLyqFZTsF4v7qriUd985x8');

//$csrf = new CSRF();
//$token =  $csrf->generateToken();
//
//echo "Token: " . $token;
//echo "<br>";
//echo "Is Valid: " . $csrf->validateToken($token);

//
//$string = "value1, value2, value3"; // Example string
//if (strpos($string, ',') !== false) {
//    $result = implode("<br>", explode(', ', $string));
//} else {
//    $result = $string;
//}
//echo  $result;


if (isset($_POST['submit'])){
    $codes = $_POST['code']; // Modify 'codes' to 'code' as it is the name of the input field
    $filteredCodes = array_filter($codes); // Remove empty values from the array
    if (!empty($filteredCodes)) {
        echo implode(",", $filteredCodes);
    }
}
?>

<form method="post" action="test.php">
    <input type="text" name="code[]"> <!-- Change 'name' attribute to 'code[]' to create an array -->
    <input type="text" name="code[]"> <!-- Change 'name' attribute to 'code[]' to create an array -->
    <input type="text" name="code[]"> <!-- Change 'name' attribute to 'code[]' to create an array -->
    <input type="submit" name="submit" value="Submit"> <!-- Add a name attribute to the submit button -->
</form>

