<?php
require_once "config.php";

$name = $address = $salary = "";
$nameErr = $addressErr = $salaryErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  //Validate Name
  $input_name = trim($_POST["name"]);
  if (empty($input_name)) {
    $nameErr = "Please enter your name.";
  } elseif (
    !filter_var($input_name, FILTER_VALIDATE_REGEXP, [
      "options" => ["regexp" => "/^[a-zA-Z\s]+$/"],
    ])
  ) {
    $nameErr = "Please enter a valid name.";
  } else {
    $name = $input_name;
  }

  //Validate address
  $input_address = trim($_POST['address']);
  if (empty($input_address)) {
    $addressErr = "Please enter your address.";
  } else {
    $address = $input_address;
  }

  //Validate Salary
  $input_salary = trim($_POST['salary']);
  if (empty($input_salary)) {
    $salaryErr = "Please enter your salary.";
  } elseif (!ctype_digit($input_salary)) {
    $salaryErr = "Please enter a positive integer value.";
  } else {
    $salary = $input_salary;
  }

  // Check errors
  if (empty($nameErr) && empty($addressErr) && empty($salaryErr)) {
    $sql = "INSERT INTO employees (name, address, salary) VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $param_name,
        $param_address,
        $param_salary
      );

      $param_name = $name;
      $param_address = $address;
      $param_salary = $salary;

      if (mysqli_stmt_execute($stmt)) {
        //Successful insertion.
        header("location: index.php");
        exit();
      } else {
        echo "ERROR: Couldn't add new record.";
      }
    }
    mysqli_stmt_close($stmt);
  }
  mysqli_close($link);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Create Record</h2>
                    </div>
                    <p>Please fill this form and submit to add employee record to the database.</p>
                    <form action="<?php echo htmlspecialchars(
                      $_SERVER["PHP_SELF"]
                    ); ?>" method="post">
                        <div class="form-group <?php echo !empty($nameErr)
                          ? 'has-error'
                          : ''; ?>">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $nameErr; ?></span>
                        </div>
                        <div class="form-group <?php echo !empty($addressErr)
                          ? 'has-error'
                          : ''; ?>">
                            <label>Address</label>
                            <textarea name="address" class="form-control"><?php echo $address; ?></textarea>
                            <span class="help-block"><?php echo $addressErr; ?></span>
                        </div>
                        <div class="form-group <?php echo !empty($salaryErr)
                          ? 'has-error'
                          : ''; ?>">
                            <label>Salary</label>
                            <input type="text" name="salary" class="form-control" value="<?php echo $salary; ?>">
                            <span class="help-block"><?php echo $salaryErr; ?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>