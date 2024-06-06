<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// 데이터베이스 연결 설정
$servername = "127.0.0.1";
$username = "cbnuopenproject";
$password = "open1234";
$dbname = "cbnuopenproject";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POST 데이터 확인
if(isset($_POST['post_id']) && isset($_POST['member_id'])) {
    $postId = $_POST['post_id'];
    $memberId = $_POST['member_id'];

    // 현재 wr_1 및 wr_4 값을 가져옴
    $sql_select = "SELECT wr_1, wr_4 FROM g6_CBNUPORTALwrite_contest WHERE wr_id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $postId);
    $stmt_select->execute();
    $stmt_select->bind_result($wr1, $wr4);
    $stmt_select->fetch();
    $stmt_select->close();

    // wr_4 배열 업데이트
    $wr4Array = json_decode($wr4, true);
    if (!$wr4Array) {
        $wr4Array = [];
    }
    if (isset($wr4Array[$memberId])) {
        unset($wr4Array[$memberId]);
    }

    // wr_1을 1 감소시킴
    $sql_update = "UPDATE g6_CBNUPORTALwrite_contest SET wr_1 = wr_1 - 1, wr_4 = ? WHERE wr_id = ? AND wr_1 > 0";
    $stmt_update = $conn->prepare($sql_update);
    $newWr4 = json_encode($wr4Array);
    $stmt_update->bind_param("si", $newWr4, $postId);
    
    if($stmt_update->execute()) {
        echo json_encode(array('success' => true, 'wr_1' => $wr1 - 1, 'wr_4' => $newWr4));
    } else {
        echo json_encode(array('success' => false, 'error' => '데이터베이스 업데이트 실패: ' . $stmt_update->error));
    }
    
    $stmt_update->close();
} else {
    // POST 데이터가 없는 경우
    echo json_encode(array('success' => false, 'error' => '게시글 ID나 회원 ID가 전송되지 않았습니다.'));
}

// 데이터베이스 연결 종료
$conn->close();
?>
