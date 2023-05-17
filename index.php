<?php

class IPMI {
    private $host;
    private $username;
    private $password;

    public function __construct($host, $username, $password) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }

    public function powerOn() {
        // Perform IPMI power on action
        $command = "ipmitool -H {$this->host} -U {$this->username} -P {$this->password} power on";
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            return true;
        } else {
            return false;
        }
    }

    public function powerOff() {
        // Perform IPMI power off action
        $command = "ipmitool -H {$this->host} -U {$this->username} -P {$this->password} power off";
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            return true;
        } else {
            return false;
        }
    }

    public function restart() {
        // Perform IPMI restart action
        $command = "ipmitool -H {$this->host} -U {$this->username} -P {$this->password} power reset";
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            return true;
        } else {
            return false;
        }
    }
}

function getCPULoad() {
    $load = sys_getloadavg();
    return $load[0];
}

function getMemoryUsage() {
    $free = shell_exec('free -b');
    $free = (string)trim($free);
    $free_arr = explode("\n", $free);
    $mem = explode(" ", $free_arr[1]);
    $mem = array_filter($mem);
    $mem = array_merge($mem);
    $memory_usage = $mem[2] / $mem[1] * 100;
    return round($memory_usage, 2);
}

// Handle IPMI form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = $_POST["host"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    $ipmi = new IPMI($host, $username, $password);

    if (isset($_POST["poweron"])) {
        $ipmi->powerOn();
    } elseif (isset($_POST["poweroff"])) {
        $ipmi->powerOff();
    } elseif (isset($_POST["restart"])) {
        $ipmi->restart();
    }
}

// Get computer metrics
$cpuUsage = getCPULoad();
$memoryUsage = getMemoryUsage();
?>

<!DOCTYPE html>
<html>
<head>
    <title>IPMI Control</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            width: 400px;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            display: block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        canvas {
            margin-top: 20px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Simpli Panel</h1>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="host">Host:</label>
        <input type="text" id="host" name="host" required>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <input type="submit" name="poweron" value="Power On">
        <input type="submit" name="poweroff" value="Power Off">
        <input type="submit" name="restart" value="Restart">

        <canvas id="cpuChart" width="400" height="200"></canvas>
        <canvas id="memoryChart" width="400" height="200"></canvas>

        <script>
            // Mock data for CPU load and memory usage (replace with actual data)
            const cpuData = [20, 35, 45, 30, 50, 40];
            const memoryData = [80, 70, 60, 75, 50, 65];

            // Create CPU load chart
            const cpuChartCtx = document.getElementById('cpuChart').getContext('2d');
            const cpuChart = new Chart(cpuChartCtx, {
                type: 'line',
                data: {
                    labels: ['1', '2', '3', '4', '5', '6'],
                    datasets: [{
                        label: 'CPU Load',
                        data: cpuData,
                        borderColor: 'rgb(75, 192, 192)',
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Create memory usage chart
            const memoryChartCtx = document.getElementById('memoryChart').getContext('2
