<?php

class ProgrammingAssessmentService {

    private function connect_to_db() {
        $dbhost = "localhost:3306";
        $dbname = "domjudge";
        $dbuser = "root";
        $dbpass = "ServerDom.12345";
        $conn = mysql_connect($dbhost, $dbuser, $dbpass);

        if (! $conn) {
            return false;
        }

        return mysql_select_db($dbname) ? $conn : false;
    }

    function setupProgassessmentModule() {
        $conn = $this->connect_to_db();

        if (! $conn) {
            return -1;
        }

        //insert entry in the contest table
        $query = "SELECT cid FROM contest WHERE contestname = \"progassessment\"";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        if (! $row) {
            $startTime = "2000-01-01 00:00:00";
            $endTime = "2100-01-01 00:00:00";
            $columns = "(contestname, activatetime, starttime, endtime)";
            $values = "(\"progassessment\", \"$startTime\", \"$startTime\", \"$endTime\")";
            $insert_query = "INSERT INTO contest " . $columns . " values " . $values;
            mysql_query($insert_query);

            //get the id of the new contest
            $result = mysql_query("SELECT LAST_INSERT_ID();");
            $row = mysql_fetch_array($result);
            return (int) $row{'LAST_INSERT_ID()'};

        } else {
            return (int) $row{'cid'};
        }
    }

    function getLanguages() {

        $languages = array();
        $conn = $this->connect_to_db();

        if (! $conn) {
            return $languages;
        }

        $query = "SELECT * FROM  language";
        $result = mysql_query($query);

        while ($row = mysql_fetch_array($result)) {
            if ($row{'allow_submit'} == 1) {
                array_push($languages, $row{'name'});
            }
        }

        mysql_close($conn);
        return $languages;
    }

    function getAllLanguagesInfo() {
        
        $languageinfo = array();
        $conn = $this->connect_to_db();

        if (! $conn) {
            return $languageinfo;
        }

        $query = "SELECT * FROM  language";
        $result = mysql_query($query);

        while ($row = mysql_fetch_array($result)) {
            if ($row{'allow_submit'} == 1) {
                array_push($languageinfo, $row{'langid'}.','.$row{'name'}.','.$row{'extension'});
            }
        }

        mysql_close($conn);
        return $languageinfo;
    }

    function addNewAssessment($id, $name, $timeLimit, $special = NULL) {

        $conn = $this->connect_to_db();

        if (! $conn) {
            return false;
        }

        $query = "SELECT cid FROM contest WHERE contestname = \"progassessment\"";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        if (! $row) {
            return false;
        }

        //check for the next available id
        $colides = true;

        while ($colides) {
            $id_query =  "SELECT probid FROM problem WHERE probid = \"$id\"";
            $id_result = mysql_query($id_query);
            $id_row = mysql_fetch_array($id_result);

            if (! $id_row) {
                $colides = false;
            } else {
                $id += 1;
            }
        }

        $cid = (int) $row{'cid'};
        $columns = "(`probid`, `cid`, `name`, `allow_submit`, `allow_judge`, `timelimit`, `special_run`, `special_compare`, `color`)";
        
        
        if ($special === NULL) {            
            $values = "(\"$id\", " . $cid . ", \"$name\", 1, 1, " . $timeLimit . ", NULL, NULL, NULL)";
        }
        else { 
            $values = "(\"$id\", " . $cid . ", \"$name\", 1, 1, " . $timeLimit .
            ", \"$special\", \"$special\", NULL)";
        }
        
        $query = "INSERT INTO `problem` " . $columns . " VALUES " . $values . ";";
                
        mysql_query($query);

        return $id;
    }
    
    function addNewAssessmentSpecial($id, $name, $timeLimit, $special) {
        $id = $this->addNewAssessment($id, $name, $timeLimit, $special);
        return $id;
    }

    function removeAssessment($id) {

        $conn = $this->connect_to_db();

        if (! $conn) {
            return;
        }

        $query = "DELETE FROM problem WHERE probid =\"$id\"";
        mysql_query($query);

        $query = "DELETE FROM problem WHERE probid like \"$id_%\"";
        mysql_query($query);
    }

    function updateAssessment($id, $name, $timeLimit, $nTestCases, $special = NULL) {
        $conn = $this->connect_to_db();

        if (! $conn) {
            return;
        }

        if ($special === NULL) {
            $query = "UPDATE `problem` SET `name`= \"$name\", `timelimit` = " . $timeLimit .
            ", `special_compare` = NULL, `special_run` = NULL WHERE `probid` = \"$id\";";
        }
        else {
            $query = "UPDATE `problem` SET `name`= \"$name\", `timelimit` = " . $timeLimit .
            ", `special_compare` = \"$special\", `special_run` = \"$special\" WHERE `probid` = \"$id\";";
        }

        mysql_query($query);


        //also update the problems that are copies of the original problem
        for ($i = 0; $i < $nTestCases; $i++) {
            $newid = "$id" . "_$i";

            if ($special === NULL) {  
                $query = "UPDATE `problem` SET `name`= \"$name\", `timelimit` = " . $timeLimit .
                ", `special_compare` = NULL, `special_run` = NULL WHERE `probid` = \"$newid\";";
            }
            else {
                $query = "UPDATE `problem` SET `name`= \"$name\", `timelimit` = " . $timeLimit .
                ", `special_compare` = \"$special\", `special_run` = \"$special\" WHERE `probid` = \"$newid\";";
            }

            mysql_query($query);
        }
    }
    
    function updateAssessmentSpecial($id, $name, $timeLimit, $nTestCases, $special) {
        $this->updateAssessment($id, $name, $timeLimit, $nTestCases, $special);
    }

    private function addAssessmentCopy($probid, $testCaseId) {

        $id = "$probid" . "_$testCaseId";

        //remove (if any) problem with the desired id
        $query = "DELETE FROM problem WHERE probid = \"$id\"";
        mysql_query($query);

        //get the original problem
        $query = "SELECT * FROM problem WHERE probid = \"$probid\"";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        $cid = $row{'cid'};
        $name = $row{'name'};
        $allow_judge = $row{'allow_judge'};
        $timeLimit = $row{'timelimit'};
        $special = $row{'special_run'};

        if ($special === NULL)
        {
            $columns = "(probid, cid, name, allow_submit, allow_judge, timelimit)";
            $values = "(\"$id\", $cid, \"$name\", 1, $allow_judge, $timeLimit)";
        }
        else
        {
            $columns = "(probid, cid, name, allow_submit, allow_judge, timelimit, special_run, special_compare)";
            $values = "(\"$id\", $cid, \"$name\", 1, $allow_judge, $timeLimit, \"$special\", \"$special\")";
        }
       
        $query = "INSERT INTO problem $columns values $values";
        mysql_query($query);
    }

    function addTestCase($probid, $input, $output, $clientid) {
        $conn = $this->connect_to_db();

        if (! $conn) {
            return -1;
        }

        if ($clientid != -1) {
            //add a copy of the original problem
            $this->addAssessmentCopy($probid, $clientid);
            $probid = "$probid" . "_$clientid";
        }

        $input = str_replace("\r\n", "\n", $input);
        $output = str_replace("\r\n", "\n", $output);
        $md5input = md5($input);
        $md5output = md5($output);
        $input = addslashes($input);
        $output = addslashes($output);

        $columns = "(md5sum_input, md5sum_output, input, output, probid)";
        $values = "(\"$md5input\", \"$md5output\", \"$input\", \"$output\", \"$probid\")";
        $query = "INSERT INTO testcase $columns values $values";

        mysql_query($query);

        //get the id of the new test case
        $result = mysql_query("SELECT LAST_INSERT_ID();");
        $row = mysql_fetch_array($result);
        return (int) $row{'LAST_INSERT_ID()'};
    }

    function removeTestCase($id) {

        $conn = $this->connect_to_db();

        if (! $conn) {
            return;
        }

        $query = "SELECT * FROM testcase WHERE id = \"$id\"";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
        $probid = $row{'probid'};

        $query = "DELETE FROM testcase WHERE id =\"$id\"";
        mysql_query($query);

        //also remove the problem copy used for the test case
        $query = "DELETE FROM problem WHERE id =\"$probid\"";
        mysql_query($query);
    }

    function updateTestCase($id, $input, $output) {

        $conn = $this->connect_to_db();

        if (! $conn) {
            return;
        }

        $input = str_replace("\r\n", "\n", $input);
        $output = str_replace("\r\n", "\n", $output);
        $md5input = md5($input);
        $md5output = md5($output);
        $input = addslashes($input);
        $output = addslashes($output);

        $query = "UPDATE testcase SET md5sum_input=\"$md5input\", md5sum_output=\"$md5output\",
                                     input=\"$input\", output=\"$output\"
                  WHERE id=\"$id\"";

        mysql_query($query);
    }

    function addParticipant($login, $name) {
        
        $conn = $this->connect_to_db();

        if (! $conn) {
            return;
        }

        $query = "SELECT * FROM team WHERE login = \"$login\"";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        if (! $row) {
            $columns = "(login, name, categoryid)";
            $values = "(\"$login\", \"$name\", 1)";
            $query = "INSERT INTO team $columns values $values";
            mysql_query($query);
        }
    }

    function participantExists($login) {
        
        $conn = $this->connect_to_db();

        if (! $conn) {
            return false;
        }

        $query = "SELECT * FROM team WHERE login = \"$login\"";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        return !$row ? false : true;
    }

    function addSubmission($participantLogin, $assessmentId, $testCasesIds, $language, $sourcecode) {

        $ids = array();
        $conn = $this->connect_to_db();

        if (! $conn) {
            return $ids;
        }

        $query = "SELECT cid FROM contest WHERE contestname = \"progassessment\"";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        if (! $row) {
            return $ids;
        }

        $test_cases_ids = array();

        if (is_array($testCasesIds->int)) {
            $test_cases_ids = $testCasesIds->int;
        } else {
            $test_cases_ids[] = $testCasesIds->int;
        }

        $cid = (int) $row{'cid'};
        $time = date("Y-m-d H:i:s");
        $sourcecode = addslashes($sourcecode);
        $columns = "(cid, teamid, probid, langid, submittime, sourcecode)";

        //insert a submission for each test case
        foreach($test_cases_ids as $i) {
            $newAssessmentId = "$assessmentId" . "_$i";
            $values = "($cid, \"$participantLogin\", \"$newAssessmentId\", \"$language\", \"$time\", \"$sourcecode\")";
            $query = "INSERT INTO submission $columns values $values";

            mysql_query($query);

            //get the id of the submission
            $result = mysql_query("SELECT LAST_INSERT_ID();");
            $row = mysql_fetch_array($result);
            $ids[] = (int) $row{'LAST_INSERT_ID()'};
        }

        return $ids;
    }

    function addCompileSubmission($participantLogin, $assessmentId, $language, $sourcecode) {
        
        $conn = $this->connect_to_db();

        if (! $conn) {
            return -1;
        }

        $query = "SELECT cid FROM contest WHERE contestname = \"progassessment\"";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        if (! $row) {
            return -1;
        }

        $cid = (int) $row{'cid'};
        $time = date("Y-m-d H:i:s");
        $sourcecode = addslashes($sourcecode);
        $columns = "(cid, teamid, probid, langid, submittime, sourcecode)";
        $values = "($cid, \"$participantLogin\", \"$assessmentId\", \"$language\", \"$time\", \"$sourcecode\")";
        $query = "INSERT INTO submission $columns values $values";

        mysql_query($query);

        //get the id of the submission
        $result = mysql_query("SELECT LAST_INSERT_ID();");
        $row = mysql_fetch_array($result);
        return (int) $row{'LAST_INSERT_ID()'};
    }

    function getSubmissionResult($id) {

        $conn = $this->connect_to_db();
        $subresult = array();

        if (! $conn) {
            return $subresult;
        }

        $query = "SELECT * FROM judging AS j LEFT JOIN judging_run AS jr ON j.judgingid = jr.judgingid WHERE submitid = $id";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        if ($row) {
            array_push($subresult, $row{'result'}, $row{'output_compile'}, $row{'output_run'}, $row{'output_diff'}, $row{'output_error'});
        }

        return $subresult;
    }
}


ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
$server = new SoapServer("frontend.wsdl", array('features' => SOAP_SINGLE_ELEMENT_ARRAYS));
$server->setClass("ProgrammingAssessmentService");
$server->handle();

?>
