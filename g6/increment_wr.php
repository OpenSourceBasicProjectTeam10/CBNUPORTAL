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
if(isset($_POST['post_id'])) {
    $postId = $_POST['post_id'];

    // wr_1 값이 성공적으로 업데이트된 경우, wr_1과 wr_2 값을 가져옴
    $sql_select = "SELECT wr_1, wr_2 FROM g6_CBNUPORTALwrite_contest WHERE wr_id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $postId);
    $stmt_select->execute();
    $stmt_select->bind_result($wr1, $wr2);
    $stmt_select->fetch();
    $stmt_select->close();

    // wr_1과 wr_2 비교하여 초과 여부 확인
    if ($wr1 < $wr2) {
        // wr_1 값이 wr_2보다 작거나 같으면 wr_1을 1 증가시킴
        $sql_update = "UPDATE g6_CBNUPORTALwrite_contest SET wr_1 = wr_1 + 1 WHERE wr_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $postId);
        
        if($stmt_update->execute()) {
            echo json_encode(array('success' => true, 'wr_1' => $wr1 + 1, 'wr_2' => $wr2));
        } else {
            echo json_encode(array('success' => false, 'error' => '데이터베이스 업데이트 실패: ' . $stmt_update->error));
        }
        
        $stmt_update->close();
    } else {
        // 인원 초과 시 경고 메시지 반환
        echo json_encode(array('success' => false, 'error' => '인원을 초과하였습니다.'));
    }
} else {
    // POST 데이터가 없는 경우
    echo json_encode(array('success' => false, 'error' => '게시글 ID가 전송되지 않았습니다.'));
}

// 데이터베이스 연결 종료
$conn->close();
?>
