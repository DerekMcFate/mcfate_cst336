<?php
session_start();

if (!isset($_SESSION['username'])) { //validates that admin has indeed logged in
    
    header("Location: index.php");
    
}

 include("../../dbConnection.php");
 $conn = getDatabaseConnection();

function getDepartmentInfo(){
    global $conn;        
    $sql = "SELECT deptName, departmentId 
            FROM tc_department 
            ORDER BY deptName";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $records = $stmt->fetchAll();
    
    return $records;
    
}

function getUserInfo($userId) {
    global $conn;    
    $sql = "SELECT * 
            FROM tc_user
            WHERE userId = $userId";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $record = $stmt->fetch();
    print_r($record);
    return $record;
}

if (isset($_GET['updateUserForm'])) { //admin has submitted form to update user
    
    $sql = "UPDATE tc_user
            SET firstName = :fName,
			    lastName = :lName,
			    email = :email,
			    universityId = :universityId,
			    phone = :phone,
			    gender = :gender,
			    role = :role,
			    departmentId = :departmentId
			    WHERE userId = :userId";
			    
	$namedParameters = array();
	$namedParameters[":userId"] = $_GET['userId'];
	$namedParameters[":fName"] = $_GET['firstName'];
	$namedParameters[":lName"] = $_GET['lastName'];
	$namedParameters[":email"] = $_GET['email'];
	$namedParameters[":universityId"] = $_GET['universityId'];
	$namedParameters[":phone"] = $_GET['phone'];
	$namedParameters[":gender"] = $_GET['gender'];
	$namedParameters[":role"] = $_GET['role'];
	$namedParameters[":departmentId"] = $_GET[':departmentId'];
	$stmt = $conn->prepare($sql);
	$stmt->execute($namedParameters);

    
}

if (isset($_GET['userId'])) {
    
    $userInfo = getUserInfo($_GET['userId']);
    
    
}



?>

<!DOCTYPE html>
<html>
    <head>
        <title> Admin: Updating User </title>
    </head>
    <body>

    <h1> Admin Section </h1>
    <h2> Updating User Info </h2>

    <fieldset>
        
        <legend> Update User </legend>
        
        <form>
            
            <input type="hidden" name="userId" value="<?=$userInfo['userId']?>" />
            First Name: <input type="text" name="firstName" required value="<?=$userInfo['firstName']?>" /> <br>
            Last Name: <input type="text" name="lastName" required value="<?=$userInfo['lastName']?>"/> <br>
            Email: <input type="text" name="email" required value="<?=$userInfo['email']?>"/> <br>
            University Id: <input type="text" name="universityId" required value="<?=$userInfo['universityId']?>"/> <br>
            Phone: <input type="text" name="phone" required value="<?=$userInfo['phone']?>"/> <br>
            Gender: <input type="radio" name="gender" value="F" id="genderF" <?=($userInfo['gender']=='F')?"checked":"" ?> required/> 
                    <label for="genderF">Female</label>
                    <input type="radio" name="gender" value="M" id="genderM" <?=($userInfo['gender']=='M')?"checked":"" ?> required/> 
                    <label for="genderM">Male</label><br>
            Role:   <select name="role">
                        <option value=""> Select One </option>
                        <option <?=($userInfo['role']=='Faculty')?"selected":"" ?>>Faculty</option>
                        <option <?=($userInfo['role']=='Student')?"selected":"" ?>>Student</option>
                        <option <?=($userInfo['role']=='Staff')?"selected":"" ?>>Staff</option>
                    </select>
            <br />
            Department: <select name="deptId">
                            <option value=""> Select One </option>
                            <?php
                            
                                $departments = getDepartmentInfo();
                                foreach ($departments as $record) {
                                    echo "<option value='$record[departmentId]' ";
                                    if($userInfo['deptId'] == $record['departmentId']) {
                                        echo "selected";
                                    }
                                    echo ">$record[deptName]</option>";
                                }
                            
                            ?>
                            
                        </select>
                        <br />
                <input type="submit" name="updateUserForm" value="Update User!"/>
        </form>
        
    </fieldset>


    </body>
</html>