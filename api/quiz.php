<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include '../connection.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_quiz':
        getQuiz($conn);
        break;
    case 'get_quiz_by_id':
        getQuizById($conn);
        break;
    case 'vote':
        submitVote($conn);
        break;
    case 'get_results':
        getResults($conn);
        break;
    case 'check_voted':
        checkVoted($conn);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

function getQuiz($conn) {
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    // Get total count of active quizzes
    $countResult = $conn->query("SELECT COUNT(*) as total FROM quizzes WHERE is_active = 1");
    $total = $countResult->fetch_assoc()['total'];
    
    // Get quiz at offset
    $stmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, option_d FROM quizzes WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1 OFFSET ?");
    $stmt->bind_param("i", $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $quiz = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'quiz' => $quiz,
            'total' => (int)$total,
            'current' => $offset + 1
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No quiz found']);
    }
}

function getQuizById($conn) {
    $quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    
    $stmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, option_d FROM quizzes WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $quiz = $result->fetch_assoc();
        echo json_encode(['success' => true, 'quiz' => $quiz]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Quiz not found']);
    }
}

function submitVote($conn) {
    $quizId = isset($_POST['quiz_id']) ? (int)$_POST['quiz_id'] : 0;
    $option = isset($_POST['option']) ? $_POST['option'] : '';
    $deviceId = isset($_POST['device_id']) ? $_POST['device_id'] : '';
    
    if (!$quizId || !$option || !$deviceId) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        return;
    }
    
    if (!in_array($option, ['a', 'b', 'c', 'd'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid option']);
        return;
    }
    
    // Check if already voted
    $checkStmt = $conn->prepare("SELECT id FROM quiz_votes WHERE quiz_id = ? AND device_id = ?");
    $checkStmt->bind_param("is", $quizId, $deviceId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Already voted', 'already_voted' => true]);
        return;
    }
    
    // Get IP address
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Insert vote
    $stmt = $conn->prepare("INSERT INTO quiz_votes (quiz_id, selected_option, device_id, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $quizId, $option, $deviceId, $ip);
    
    if ($stmt->execute()) {
        // Get correct answer
        $correctStmt = $conn->prepare("SELECT correct_option FROM quizzes WHERE id = ?");
        $correctStmt->bind_param("i", $quizId);
        $correctStmt->execute();
        $correctResult = $correctStmt->get_result()->fetch_assoc();
        $isCorrect = ($correctResult['correct_option'] === $option);
        
        // Get updated results
        $results = getVoteResults($conn, $quizId);
        
        echo json_encode([
            'success' => true,
            'is_correct' => $isCorrect,
            'correct_option' => $correctResult['correct_option'],
            'results' => $results
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to submit vote']);
    }
}

function getResults($conn) {
    $quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    
    if (!$quizId) {
        echo json_encode(['success' => false, 'error' => 'Quiz ID required']);
        return;
    }
    
    $results = getVoteResults($conn, $quizId);
    
    // Get correct answer
    $correctStmt = $conn->prepare("SELECT correct_option FROM quizzes WHERE id = ?");
    $correctStmt->bind_param("i", $quizId);
    $correctStmt->execute();
    $correctResult = $correctStmt->get_result()->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'correct_option' => $correctResult['correct_option'],
        'results' => $results
    ]);
}

function checkVoted($conn) {
    $quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $deviceId = isset($_GET['device_id']) ? $_GET['device_id'] : '';
    
    if (!$quizId || !$deviceId) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        return;
    }
    
    $stmt = $conn->prepare("SELECT selected_option FROM quiz_votes WHERE quiz_id = ? AND device_id = ?");
    $stmt->bind_param("is", $quizId, $deviceId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $vote = $result->fetch_assoc();
        $results = getVoteResults($conn, $quizId);
        
        // Get correct answer
        $correctStmt = $conn->prepare("SELECT correct_option FROM quizzes WHERE id = ?");
        $correctStmt->bind_param("i", $quizId);
        $correctStmt->execute();
        $correctResult = $correctStmt->get_result()->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'voted' => true,
            'selected_option' => $vote['selected_option'],
            'correct_option' => $correctResult['correct_option'],
            'results' => $results
        ]);
    } else {
        echo json_encode(['success' => true, 'voted' => false]);
    }
}

function getVoteResults($conn, $quizId) {
    // Get vote counts per option
    $stmt = $conn->prepare("
        SELECT 
            selected_option,
            COUNT(*) as count
        FROM quiz_votes 
        WHERE quiz_id = ?
        GROUP BY selected_option
    ");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $votes = ['a' => 0, 'b' => 0, 'c' => 0, 'd' => 0];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        $votes[$row['selected_option']] = (int)$row['count'];
        $total += (int)$row['count'];
    }
    
    // Calculate percentages
    $percentages = [];
    foreach ($votes as $option => $count) {
        $percentages[$option] = $total > 0 ? round(($count / $total) * 100) : 0;
    }
    
    return [
        'votes' => $votes,
        'percentages' => $percentages,
        'total' => $total
    ];
}

$conn->close();
?>
