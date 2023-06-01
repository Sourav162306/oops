<?php
class DatabaseConnection {
    private $conn;

    public function __construct($servername, $username, $password, $dbname) {
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parent_table";

$db = new DatabaseConnection($servername, $username, $password, $dbname);
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parentId = $_POST['parent'];
    $name = $_POST['name'];
    $datetime = date("Y-m-d H:i:s");
    $sql = "INSERT INTO parent_records (createdDate, name, parentId)
            VALUES ('$datetime', '$name', '$parentId')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

function showParentAndChild($parentId, $level = 0) {
    global $conn;

    $sqlQuery = "SELECT * FROM parent_records WHERE parentId='$parentId'";
    $result = mysqli_query($conn, $sqlQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "<ul>\n";

        while ($row = mysqli_fetch_assoc($result)) {
            echo str_repeat("\t", $level) . "<li>" . $row['name'];
            showParentAndChild($row['id'], $level + 1);
            echo "</li>";
        }

        echo "</ul>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }
    </style>
</head>
<body>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form method="post" id="myForm">
                <label for="parent">Parent</label>
                <br/>
                <select name="parent" id="parent">
                    <option>Select Category</option>
                    <option value="0">None</option>
                    <?php
                    $sqlData = "SELECT * FROM parent_records";
                    $result = mysqli_query($conn, $sqlData);
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                    ?>
                </select>
                <br/>
                <br/>
                <label for="name">Name</label>
                <br/>
                <input type="text" name="name" id="name">
                <br/>
                <br/>
                <button id="submit" type="submit">Submit</button>
            </form>
        </div>
    </div>
    

    <?php
    showParentAndChild(0);
    ?>

    <button id="nameGenerateBtn">Add Member</button>

    <script>
        $(document).ready(function() {
            // Modal
            var modal = $("#myModal");
            var btn = $("#nameGenerateBtn");
            var span = $(".close");

            btn.on("click", function() {
                modal.css("display", "block");
            });

            span.on("click", function() {
                modal.css("display", "none");
            });
            $("#generateForm").on("submit", function(e) {
                e.preventDefault();
                var generatedName = "Generated Name";
                $("#generatedName").val(generatedName);
                modal.css("display", "none");
                $("#myForm").submit();
            });
        });
    </script>
    <style> 
        #parent{
            width: 100%;
            border: 1px solid #000000;
            border-radius: 3px;
        }
        #name{
            width: 100%;
            border: 1px solid #000000;
            border-radius: 3px;
        }
        #submit{
            background: #ab96db;
            color: #fff;
            width: 100px;
            height: 30px;
            border: 1px solid #000000;
            border-radius: 10px;
        }
        #nameGenerateBtn{
            background: #3f3fa3;
            color: #fff;
            height: 35px;
            width: 100px;
            border: 1px solid #000;
            border-radius: 10px;
        }
    </style>
</body>
</html>
