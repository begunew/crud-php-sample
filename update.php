<?php

require_once "config.php";

$name = $address = $salary = "";
$nameErr = $addressErr = $salaryErr = "";

if (isset($_POST["id"]) && !empty($_POST["id"])) {
  $id = $_POST["id"];

  //Validate Name
  $input_name = trim($_POST["name"]);
  if (empty($input_name)) {
    $nameErr = "Please enter a name.";
  } elseif (
    !filter_var($input_name, FILTER_VALIDATE_REGEXP, [
      "options" => ["regexp" => "/^[a-zA-Z\s]+$/"],
    ])
  ) {
    $nameErr = "Please enter a valid name.";
  } else {
    $name = $input_name;
  }

  //Validate Address
  $input_address = trim($_POST['address']);
  if (empty($input_address)) {
    $addressErr = "Please enter an address.";
  } else {
    $address = $input_address;
  }

  //Validate Salary

  $input_salary = trim($_POST['salary']);
  if (empty($input_salary)) {
    $salaryErr = "Please enter a salary.";
  } elseif (!ctype_digit($input_salary)) {
    $salaryErr = "Please enter a positive integer value.";
  } else {
    $salary = $input_salary;
  }

  if (empty($nameErr) && empty($addressErr) && empty($salaryErr)) {
    $sql = "UPDATE employees SET name=?, address=?, salary=? WHERE id=?";

    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param(
        $stmt,
        "ssss",
        $param_name,
        $param_address,
        $param_salary,
        $param_id
      );

      $param_name = $name;
      $param_address = $address;
      $param_salary = $salary;
      $param_id = $id;

      if (mysqli_stmt_execute($stmt)) {
        header("location: index.php");
        exit();
      } else {
        echo "ERROR: Something went wrong!";
      }
    }
    mysqli_stmt_close($stmt);
  }
  mysqli_close($link);
} else {
  if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    $sql = "SELECT * FROM employees WHERE id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "i", $param_id);
      $param_id = $id;

      if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

          $name = $row['name'];
          $address = $row['address'];
          $salary = $row['salary'];
        } else {
          header("location: error.php");
          exit();
        }
      } else {
        echo "ERROR: Something went wrong!";
      }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
  } else {
    header("location: error.php");
    exit();
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }

        textarea {
            resize: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(
                      basename($_SERVER['REQUEST_URI'])
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
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>