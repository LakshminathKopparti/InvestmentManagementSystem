<?php
$servername = "localhost";
$username = "root";
$password = "Lucky@24";
$dbname = "investment";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    // Query to fetch all investments and net portfolio value
    $sql = "SELECT 
                i.asset_type,
                i.amount AS invested_amount,
                i.purchase_date,
                p.current_value,
                p.net_gain_or_loss
            FROM investments i
            JOIN portfolio_performance p ON i.investment_id = p.investment_id
            WHERE i.investor_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h1>User Investments</h1>";
        echo "<table border='1'>
                <tr>
                    <th>Asset Type</th>
                    <th>Invested Amount</th>
                    <th>Purchase Date</th>
                    <th>Current Value</th>
                    <th>Net Gain/Loss</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['asset_type']}</td>
                    <td>\${$row['invested_amount']}</td>
                    <td>{$row['purchase_date']}</td>
                    <td>\${$row['current_value']}</td>
                    <td>\${$row['net_gain_or_loss']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<h1>No Investments Found for User</h1>";
    }

    // Query to fetch total portfolio value
    $sql_total = "SELECT SUM(current_value) AS total_portfolio_value
                  FROM portfolio_performance
                  WHERE investor_id = ?";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("i", $user_id);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();

    if ($row = $result_total->fetch_assoc()) {
        echo "<h2>Total Portfolio Value: $" . $row['total_portfolio_value'] . "</h2>";
    }

    // Query to calculate total profit
    $sql_profit = "SELECT SUM(net_gain_or_loss) AS total_profit
                   FROM portfolio_performance
                   WHERE investor_id = ?";
    $stmt_profit = $conn->prepare($sql_profit);
    $stmt_profit->bind_param("i", $user_id);
    $stmt_profit->execute();
    $result_profit = $stmt_profit->get_result();

    if ($row = $result_profit->fetch_assoc()) {
        echo "<h2>Total Profit: $" . $row['total_profit'] . "</h2>";
    }
} else {
    echo "User ID not provided.";
}

$conn->close();
?>
