<?php

function connectToDatabase() {
    $host = "localhost";
    $username = "cron";
    $password = "1234";
    $database = "voicecatch";

    $db = new mysqli($host, $username, $password, $database);

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    return $db;
}

function processCalls() {
    $db = connectToDatabase();
    $recordingPath = '/var/www/recordings/files/'; // Set the actual path to your recordings

    $query = "SELECT * FROM calldetails WHERE isprocessed = 0 AND EndTime <> '0000-00-00 00:00:00' LIMIT 50";
    $result = $db->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as $row) {
        $callid = $row['callidpk'];
        $type = $row['Type'];
        $filename = $row['Filename'];
        $starttime = $row['StartTime'];
        $endtime = $row['EndTime'];
        $phonenumber = $row['PhoneNumber'];
        $pbxextn = null;

        // Add -2 seconds to the start time to match/fetch smdr time
        $startTimeDateTime = new DateTime($starttime);
        $startTimeDateTime->modify('-5 seconds');
        $starttime = $startTimeDateTime->format('Y-m-d H:i:s');

        $updateFlag = false;

        echo "Processing call $callid - Type: $type, Filename: $filename, PhoneNumber: $phonenumber\n";

        if ($type == 'incoming') {
            if (!file_exists($recordingPath . $filename)) {
                $type = 'missed';
                echo "Recording not found for call $callid. Setting type to 'missed'\n";
            } else {
                $query = "SELECT calledparty, time, sec FROM invoip WHERE LOCATE('$phonenumber', callingparty) > 0 AND TIME(time) BETWEEN TIME('$starttime') AND TIME('$endtime') AND calltype IN ('N', 'U', 'I', 'G', 'Q', 'C', 'F','T')";
                echo "Query: $query\n";
                $result = $db->query($query);
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                if (count($rows) > 0) {
                    $pbxextn = $rows[0]['calledparty'];
                    echo "Found initial pbxextn value: $pbxextn\n";

                    while (true) {
                        $time = $rows[0]['time'];
                        $sec = $rows[0]['sec'];
                        $sec += 30;

                        $innerQuery = "SELECT * FROM invoip WHERE `time` < ADDTIME('$time', '$sec') AND LOCATE('$phonenumber', calledno) > 0 AND calltype = 'A' LIMIT 1";
                        echo "Inner Query: $innerQuery\n";
                        $innerResult = $db->query($innerQuery);
                        $innerRows = $innerResult->fetch_all(MYSQLI_ASSOC);

                        if (count($innerRows) > 0) {
                            $pbxextn .= ',' . $innerRows[0]['calledparty'];
                            echo "Updated pbxextn value: $pbxextn\n";
                        } else {
                            break;
                        }
                    }
                }
            }
        } else {
            if (!file_exists($recordingPath . $filename)) {
                $type = 'noanswer';
                echo "Recording not found for call $callid. Setting type to 'noanswer'\n";
            } else {
                $query = "SELECT calledparty, time, sec FROM ogvoip WHERE LOCATE('$phonenumber', calledno) > 0 AND TIME(time) BETWEEN TIME('$starttime') AND TIME('$endtime') AND calltype in ('I','U')";
                echo "Query: $query\n";
                $result = $db->query($query);
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                if (count($rows) > 0) {
                    $pbxextn = $rows[0]['calledparty'];
                    echo "Found initial pbxextn value: $pbxextn\n";

                    while (true) {
                        $time = $rows[0]['time'];
                        $sec = $rows[0]['sec'];
                        $sec += 30;

                        $innerQuery = "SELECT * FROM ogvoip WHERE `time` < ADDTIME('$time', '$sec') AND LOCATE('$phonenumber', calledno) > 0 AND calltype = 'z' LIMIT 1";
                        echo "Inner Query: $innerQuery\n";
                        $innerResult = $db->query($innerQuery);
                        $innerRows = $innerResult->fetch_all(MYSQLI_ASSOC);

                        if (count($innerRows) > 0) {
                            $pbxextn .= ',' . $innerRows[0]['calledparty'];
                            echo "Updated pbxextn value: $pbxextn\n";
                        } else {
                            break;
                        }
                    }
                }
            }
        }

        $query = "UPDATE calldetails SET `Type` = '$type', pbxextn = '$pbxextn', isprocessed = 1 WHERE callidpk = $callid";
        $db->query($query);
        echo "Updated call $callid - Type: $type, pbxextn: $pbxextn\n";
    }

    $db->close();
}

// To run this code, you can call the processCalls() function when the script is accessed through a web browser.
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    processCalls();
}
