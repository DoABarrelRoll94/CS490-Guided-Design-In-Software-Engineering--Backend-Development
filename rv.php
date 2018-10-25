<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

class MYSQL
{
   private $servername = "sql2.njit.edu";
   private $username = "mem52";
   private $password = "3WME4JOq";
   private $conn;

   function __construct($newConn)
   {
        $this->conn = $newConn;
   }

   function validate($user, $pass)
   {
      if($this->conn)
      {
         if ($this->validateInstructor($user, $pass))
         {
            return 1;
         }
         else if($this->validateStudent($user, $pass))
         {
            return 2;
         }
         else
         {
            return 0;
         }
      }
      else
      {
        return "Conn Fail.";
      }
   }

   function validateStudent($user, $pass)
   {
       if($stmt = $this->conn->prepare("SELECT Password FROM Students WHERE User = ?"))
       {
          $stmt->bind_param("s", $user);
          $stmt->execute();
          $stmt->bind_result($result);
          $stmt->fetch();
          $stmt->close();
          if(password_verify($pass, $result))
          {
             return true;
          }
       else
       {
          $error = $this->conn->errno . ' ' . $this->conn->error;
          echo $error;
       }
       return false;
   }

   function validateInstructor($user, $pass)
   {
      if($stmt = $this->conn->prepare("SELECT Password FROM Instructor WHERE User = ?"))
      {
         $stmt->bind_param("s", $user);
         $stmt->execute();
         $stmt->bind_result($result);
         $stmt->fetch();
         $stmt->close();
         if(password_verify($pass, $result))
         {
            return true;
         }
      }
      else
      {
         $error = $this->$conn->errno . ' ' . $this->$conn->error;
         echo $error;}
     return false;
   }

   function fillQuestionBank($qA)
   {
      if($stmt = $this->conn->prepare("REPLACE INTO QuestionBank (Title, Details, Difficulty, FunctionName, Topic, testcases, result1, result2, result3, result4, result5, input1, i\
nput2, input3, input4, input5) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"))
      {
         $stmt->bind_param("sssssissssssssss", $qA["Tit"], $qA["Details"], $qA["Diff"], $qA["FuncName"],$qA["Top"], $qA["TestCases"], $qA["result1"], $qA["result2"], $qA["result3"]\
, $qA["result4"], $qA["result5"], $qA["input1"], $qA["input2"], $qA["input3"], $qA["input4"], $qA["input5"]);
         if(!$stmt->execute())
         {
            return "Insertion fail";
         }
         $stmt->close();
      }
      return "fd";
   }

   function getQuestion($qid)
   {
      if($stmt = $this->conn->prepare("SELECT QuestionBank.*, Exam.QuestionScore FROM QuestionBank,Exam WHERE ID = ?"))
      {
           $stmt->bind_param("i",$qid);
           $stmt->bind_result($result0,$result1, $result2, $result3, $result4, $result5,
           $result6, $result7, $result8, $result9, $result10, $result11, $result12, $result13,
           $result14, $result15, $result16, $result17);$stmt->execute();
           $stmt->fetch();
           $arr = array(
                        "qid"             => $result0,
                        "title"           => $result1,
                        "details"         => $result2,
                        "difficulty"      => $result3,
                        "function_name"   => $result4,
                        "topic"           => $result5,
                        "test_cases"      => $result6,
                        "result1"         => $result7,
                        "result2"         => $result8,
                        "result3"         => $result9,
                        "result4"         => $result10,
                        "result5"         => $result11,
                        "input1"          => $result12,
                        "input2"          => $result13,
                        "input3"          => $result14,
                        "input4"          => $result15,
                        "input5"          => $result16,
                        "points"          => $result17
                  );
         $stmt->close();
         }
          return json_encode($arr);
   }
   function getQuestionWithPoints($qid)
   {
      if($stmt = $this->conn->prepare("SELECT QuestionBank.*, Exam.QuestionScore FROM QuestionBank, Exam WHERE QuestionBank.ID = ? AND Exam.QuestionID = ?"))
      {
           $stmt->bind_param("ii",$qid, $qid);
           $stmt->bind_result($result0,$result1, $result2, $result3, $result4, $result5,
           $result6, $result7, $result8, $result9, $result10, $result11, $result12, $result13,
           $result14, $result15, $result16, $result17);
           $stmt->execute();
           $stmt->fetch();
           $arr = array(
                        "qid"             => $result0,
                        "title"           => $result1,
                        "details"         => $result2,
                        "difficulty"      => $result3,
                        "function_name"   => $result4,
                        "topic"           => $result5,
                        "test_cases"      => $result6,
                        "result1"         => $result7,
                        "result2"         => $result8,
                        "result3"         => $result9,
                        "result4"         => $result10,
                        "result5"         => $result11,
                        "input1"          => $result12,
                        "input2"          => $result13,
                        "input3"          => $result14,
						"input4"          => $result15,
                        "input5"          => $result16,
                        "points"          => $result17
                  );
            $stmt->close();
            }
            return json_encode($arr);
   }
   function getQuestionList()
   {
        if($stmt = $this->conn->prepare("SELECT ID, Title FROM QuestionBank"))
        {
           $stmt->bind_result($result, $result2);
           if($stmt->execute())
           {
              $x = 0;
              while($stmt->fetch())
              {
                 $arr[] = array($x => array(
                              "id"    => $result,
                              "title" => $result2
                 ));
                 $x = $x + 1;
              }
              return json_encode($arr);
           }
        $stmt->close();}
   }

   function addQuestionToExam($qid, $title, $question_score)
   {
      if($stmt = $this->conn->prepare("INSERT INTO Exam(QuestionID, Title, QuestionScore) VALUES(?,?,?)"))
        {
           $stmt->bind_param("isi", $qid, $title, $question_score);
           $stmt->execute();
           $stmt->close();
        }
   }

   function storeStudentAnswer($arr)
   {
       if($stmt = $this->conn->prepare("REPLACE INTO TakenTestResults(StudentID, QuestionID, Answers, FinalScore) VALUES(?,?,?,?)"))
       {
          $stmt->bind_param("siss", $arr["user"], $arr["qid"], $arr["answer"], $arr["send_db"]);
          $stmt->execute();
          $stmt->close();
       }
   }

   function getStudentAnswer($arr)
   {
       if($stmt = $this->conn->prepare("SELECT Answers FROM TakenTestResults WHERE StudentID = ? AND QuestionID = ?"))
        {$stmt->bind_param("si", $arr["user"], $arr["qid"]);
           $stmt->bind_result($result);
           $stmt->execute();
           $stmt->fetch();
           $arr = array(
                       "user" => $result
           );
           $stmt->close();
        }
        return json_encode($arr);
   }

   function releaseScores()
   {
      if($stmt = $this->conn->prepare("UPDATE TakenTestResults SET ReleasedScore = 'yes'"))
       {
           $stmt->execute();
           $stmt->close();
       }
   }

   function viewScores($arr)
   {
       if($stmt = $this->conn->prepare("SELECT ReleasedScore FROM TakenTestResults WHERE StudentID = ? LIMIT 1"))
       {
          $stmt->bind_param("s", $arr["user"]);
          $stmt->execute();$stmt->bind_result($result);
          $stmt->fetch();
          $stmt->close();
          if($result == 'yes')
          {
             return json_encode(array("db_resp" => true));
          }
       }
       return json_encode(array("db_resp" => false));
   }

   function getExamQuestionList()
   {
      if($stmt = $this->conn->prepare("SELECT QuestionID, Title FROM Exam"))
      {
          $stmt->execute();
          $stmt->bind_result($result, $result2);
          $x = 0;
          $arr = [];
          while($stmt->fetch())
          {
             $arr[] = array($x => array(
                          "id"    => $result,
                          "title" => $result2,
                      ));
             $x = $x + 1;
          }$stmt->close();
          return json_encode($arr);
       }
   }

   function auxiliaryScores($arr)
   {
      if($stmt = $this->conn->prepare("SELECT QuestionScore FROM Exam WHERE QuestionID = ? "))
      {
         $stmt->bind_param("i", $arr);
         $stmt->execute();
         $stmt->bind_result($result);
         $stmt->fetch();
         $arr = array(
                       "question_score"  => $result
                  );
         $stmt->close();
         return json_encode($arr);
       }
   }

   function getStudentScore($arr)
   {
      if($stmt = $this->conn->prepare("SELECT Answers, FinalScore FROM TakenTestResults WHERE QuestionID = ? AND StudentID = ?"))
      {
        $stmt->bind_param("is", $arr["qid"], $arr["user"]);
        $stmt->execute();$stmt->bind_result($result, $result2);
        $stmt->fetch();
        $arr = array(
                    "answer"      => $result,
                    "final_score" => $result2
               );
       $stmt->close();
       return json_encode($arr);
      }
   }

   function filterQuestionList($arr)
   {
        if($stmt = $this->conn->prepare("SELECT ID, Title FROM QuestionBank WHERE Difficulty LIKE ? AND (FunctionName LIKE ? OR Topic LIKE ? OR Title LIKE ?)"))
        {
           $diff = '%'.$arr["filter_diff"].'%';
           $other = '%'.$arr["filter_other"].'%';
           $stmt->bind_param("ssss", $diff, $other, $other, $other);
           $stmt->bind_result($result, $result2);
           $x = 0;
           $ar = [];
           if($stmt->execute())
           {
              while($stmt->fetch())
              {
                 $ar[] = array($x => array(
                              "id"    => $result,                                                      "title" => $result2
                          ));
                 $x = $x + 1;
              }
              return json_encode($ar);
           }
        $stmt->close();
        }
   }

   function removeQuestionFromExam($arr)
   {
      if($stmt = $this->conn->prepare("DELETE FROM Exam WHERE QuestionID = ?"))
      {
         $stmt->bind_param("i", $arr);
         $stmt->execute();
      }
      $stmt->close();
   }

   function createNewExam($arr)
   {
      if($stmt = $this->conn->prepare("INSERT INTO ExamList(Title, Status) VALUES (?,'unpublished')"))
      {
         $stmt->bind_param("s", $arr);
         $stmt->execute();
         $arr = array(
                     "exam_ID" => $stmt->insert_id,
                     "title"  =>  $arr
                     );
        $stmt->close();
        return json_encode($arr);
      }
   }

   function getExamList($status)
   {
      if($stmt = $this->conn->prepare("SELECT ID, Title FROM ExamList"))
      {
         $stmt->execute();
         $stmt->bind_result($result, $result2);
         $x = 0;
         $arr = [];
         while($stmt->fetch())
         {
            $arr[] = array($x => array(
                         "id"    => $result,
                         "title" => $result2,
                      ));
            $x = $x + 1;
         }
         $stmt->close();
         return json_encode($arr);
      }
   function changeExamStatus($arr)
   {
      if($stmt = $this->conn->prepare("UPDATE ExamList SET ReleasedScore = 'yes'"))
      {
        $stmt->execute();
        $stmt->close();
      }
   }
}


//****************************************************************************//
                             //END OF MYSQL CLASS//
//****************************************************************************//


//Request username and password from client
//$name = $_REQUEST["function_name"];

//Connect to database
$conn = mysqli_connect("sql2.njit.edu", "mem52", "3WME4JOq", "mem52");
$mysql = new MYSQL($conn);

//Validate User and Password
if(isset($_POST['action']) && $_POST['action'] == 'login'){
   $user = $_REQUEST["user"];
   $pass = $_REQUEST["pass"];
   $result = $mysql->validate($user, $pass);
   $result = array('db_response'=>$result);
   echo json_encode($result);
}


//****************************************************************************//
                           //START OF FUNCTION CALLS//
//****************************************************************************//


//Add Questions to DB
if(isset($_POST['action']) && $_POST['action'] == 'add_question')
{
   $qA = array(
                 "Tit"         => $_POST["title"],
                 "Details"     => $_POST["description"],
                 "Diff"        => $_POST["difficulty"],
                 "FuncName"    => $_POST["function_name"],
                 "Top"         => $_POST["topic"],
                 "TestCases"   => $_POST["test_cases"],
                 "input1"      => $_POST["input1"],
                 "input2"      => $_POST["input2"],
                 "input3"      => $_POST["input3"],                                       "input4"      => $_POST["input4"],
                 "input5"      => $_POST["input5"],
                 "result1"     => $_POST['result1'],
                 "result2"     => $_POST['result2'],
                 "result3"     => $_POST['result3'],
                 "result4"     => $_POST['result4'],
                 "result5"     => $_POST['result5'],
   );
   echo $mysql->fillQuestionBank($qA);
}

//Get a question from DB
if(isset($_POST['action']) && $_POST['action'] == 'get_question')
{
   echo $mysql->getQuestion($_POST['qid']);
}

if(isset($_POST['action'])  && $_POST['action'] == 'get_question_with_points')
{
   echo $mysql->getQuestionWithPoints($_POST['qid']);
}

//Get the question list from DB
if(isset($_POST['action']) && $_POST['action'] == 'get_question_list')
{
   echo $mysql->getQuestionList();
}
//Add a question to the exam
if(isset($_POST['action']) && $_POST['action'] == 'add_question_to_exam')
{
   echo $mysql->addQuestionToExam($_POST['qid'], $_POST['title'], $_POST['question_score']);
}

//Store a student answer
if(isset($_POST['action']) && $_POST['action'] == 'store_student_answer')
{
   $ans = array(
                "user"        => $_POST["user"],
                "qid"         => $_POST["qid"],
                "answer"      => $_POST["answer"],
                "send_db"     => $_POST["send_db"]

   );

   echo $mysql->storeStudentAnswer($ans);
}

//Get student answer
if(isset($_POST['action']) && $_POST['action'] == 'get_student_answer')
{
   $arr = array(
               "user" => $_POST["user"],
               "qid"  => $_POST["qid"]   );

   echo $mysql->getStudentAnswer($arr);
}

//Release the scores
if(isset($_POST['action']) && $_POST['action'] == 'release_scores')
{
   echo $mysql->releaseScores();
}

//View the scores
if(isset($_POST['action']) && $_POST['action'] == 'view_scores')
{
   $arr = array(
               "user" => $_POST["user"]
   );

   echo $mysql->viewScores($arr);
}

//Get the exam question list
if(isset($_POST['action']) && $_POST['action'] == 'get_exam_question_list')
{
   echo $mysql->getExamQuestionList();
}
if(isset($_POST['action']) && $_POST['action'] == 'exam_point_value')
{
   echo $mysql->auxiliaryScores($_POST["qid"]);
}

if(isset($_POST['action']) && $_POST['action'] == 'get_student_score')
{
   $arr = array(
                "user" => $_POST["user"],
                "qid"  => $_POST["qid"]
          );

   echo $mysql->getStudentScore($arr);
}

if(isset($_POST['action']) && $_POST['action'] == 'filter_question_list')
{
    $arr = array(
                "filter_diff"   => $_POST["filter_diff"],
                "filter_other"  => $_POST["filter_other"]
           );
    echo $mysql->filterQuestionList($arr);
}

if(isset($_POST['action'])  && $_POST['action'] == 'remove_question_from_exam')
{
    echo $mysql->removeQuestionFromExam($_POST["qid"]);}

if(isset($_POST['action'])  && $_POST['action'] == 'create_new_exam')
{
    echo $mysql->createNewExam($_POST["title"]);
}

if(isset($_POST['action'])  && $_POST['action'] == 'get_exam_list')
{
   echo $mysql->getExamList($_POST["status"]);
}

if(isset($_POST['action'])  && $_POST['action'] == 'change_exam_status')
{
   $arr = array(
               "id" => $_POST["id"],
               "status" => $_POST["status"]
               );

   echo $mysql->changeExamStatus($arr);
}

?>