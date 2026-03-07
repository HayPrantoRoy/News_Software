<?php
include 'auth_check.php';
include '../connection.php';

// Check if user has permission to view this page
if (!$can_view) {
    header("Location: dashboard.php");
    exit();
}

// Handle Add/Edit/Delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $id = $_POST['id'] ?? null;
        $question = trim($_POST['question'] ?? '');
        $option_a = trim($_POST['option_a'] ?? '');
        $option_b = trim($_POST['option_b'] ?? '');
        $option_c = trim($_POST['option_c'] ?? '');
        $option_d = trim($_POST['option_d'] ?? '');
        $correct_option = $_POST['correct_option'] ?? 'a';
        $is_active = isset($_POST['status']) ? 1 : 0;
        
        if (empty($question) || empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d)) {
            $_SESSION['error'] = "All fields are required!";
        } else {
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO quizzes (question, option_a, option_b, option_c, option_d, correct_option, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssi", $question, $option_a, $option_b, $option_c, $option_d, $correct_option, $is_active);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Quiz added successfully!";
                } else {
                    $_SESSION['error'] = "Failed to add quiz: " . $conn->error;
                }
                $stmt->close();
            } else {
                $stmt = $conn->prepare("UPDATE quizzes SET question=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_option=?, is_active=? WHERE id=?");
                $stmt->bind_param("ssssssii", $question, $option_a, $option_b, $option_c, $option_d, $correct_option, $is_active, $id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Quiz updated successfully!";
                } else {
                    $_SESSION['error'] = "Failed to update quiz: " . $conn->error;
                }
                $stmt->close();
            }
        }
    }
    
    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        
        // Delete votes first
        $stmt = $conn->prepare("DELETE FROM quiz_votes WHERE quiz_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        // Delete quiz
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Quiz deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete quiz: " . $conn->error;
        }
        $stmt->close();
    }
    
    header("Location: manage_quizzes.php");
    exit();
}

// Fetch all quizzes
$quizzes = [];
$result = $conn->query("SELECT q.id, q.question, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.is_active, q.created_at, (SELECT COUNT(*) FROM quiz_votes WHERE quiz_id = q.id) as vote_count FROM quizzes q ORDER BY q.created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quizzes - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .content-area {
            padding: 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #308e87;
            color: white;
        }
        
        .btn-primary:hover {
            background: #267872;
        }
        
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        
        .btn-danger:hover {
            background: #b91c1c;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #374151;
            font-size: 13px;
            text-transform: uppercase;
        }
        
        td {
            font-size: 14px;
            color: #4b5563;
        }
        
        tr:hover {
            background: #f9fafb;
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-active {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-inactive {
            background: #fef2f2;
            color: #991b1b;
        }
        
        .correct-badge {
            background: #16a34a;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #308e87;
        }
        
        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .option-group {
            position: relative;
        }
        
        .option-label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: #308e87;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }
        
        .option-group input {
            padding-left: 45px;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .radio-item input {
            width: 18px;
            height: 18px;
            accent-color: #308e87;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input {
            width: 18px;
            height: 18px;
            accent-color: #308e87;
        }
        
        .modal-footer {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        
        .vote-count {
            background: #e0f2fe;
            color: #0369a1;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .question-text {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <div class="content-area">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-question-circle"></i> Manage Quizzes</h1>
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Add Quiz
            </button>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Options</th>
                            <th>Correct</th>
                            <th>Votes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quizzes)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">
                                    No quizzes found. Click "Add Quiz" to create one.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quizzes as $quiz): ?>
                                <tr>
                                    <td><?= $quiz['id']; ?></td>
                                    <td class="question-text" title="<?= htmlspecialchars($quiz['question']); ?>">
                                        <?= htmlspecialchars($quiz['question']); ?>
                                    </td>
                                    <td>
                                        <small>
                                            A: <?= htmlspecialchars(mb_substr($quiz['option_a'], 0, 15)); ?><?= mb_strlen($quiz['option_a']) > 15 ? '...' : ''; ?><br>
                                            B: <?= htmlspecialchars(mb_substr($quiz['option_b'], 0, 15)); ?><?= mb_strlen($quiz['option_b']) > 15 ? '...' : ''; ?><br>
                                            C: <?= htmlspecialchars(mb_substr($quiz['option_c'], 0, 15)); ?><?= mb_strlen($quiz['option_c']) > 15 ? '...' : ''; ?><br>
                                            D: <?= htmlspecialchars(mb_substr($quiz['option_d'], 0, 15)); ?><?= mb_strlen($quiz['option_d']) > 15 ? '...' : ''; ?>
                                        </small>
                                    </td>
                                    <td><span class="correct-badge"><?= strtoupper($quiz['correct_option']); ?></span></td>
                                    <td><span class="vote-count"><?= $quiz['vote_count']; ?> votes</span></td>
                                    <td>
                                        <span class="status-badge <?= $quiz['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                            <?= $quiz['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <?php if ($can_edit): ?>
                                        <button class="btn btn-primary btn-sm" onclick="editQuiz(<?= htmlspecialchars(json_encode($quiz)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($can_delete): ?>
                                        <button class="btn btn-danger btn-sm" onclick="deleteQuiz(<?= $quiz['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if (!$can_edit && !$can_delete): ?>
                                        <span style="color:#999;">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div class="modal" id="quizModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Quiz</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" id="quizForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="quizId">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Question *</label>
                        <textarea name="question" id="question" class="form-control" required placeholder="Enter quiz question..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Options *</label>
                        <div class="options-grid">
                            <div class="option-group">
                                <span class="option-label">A</span>
                                <input type="text" name="option_a" id="option_a" class="form-control" required placeholder="Option A">
                            </div>
                            <div class="option-group">
                                <span class="option-label">B</span>
                                <input type="text" name="option_b" id="option_b" class="form-control" required placeholder="Option B">
                            </div>
                            <div class="option-group">
                                <span class="option-label">C</span>
                                <input type="text" name="option_c" id="option_c" class="form-control" required placeholder="Option C">
                            </div>
                            <div class="option-group">
                                <span class="option-label">D</span>
                                <input type="text" name="option_d" id="option_d" class="form-control" required placeholder="Option D">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Correct Answer *</label>
                        <div class="radio-group">
                            <label class="radio-item">
                                <input type="radio" name="correct_option" value="a" checked> Option A
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="correct_option" value="b"> Option B
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="correct_option" value="c"> Option C
                            </label>
                            <label class="radio-item">
                                <input type="radio" name="correct_option" value="d"> Option D
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" name="status" id="status" checked>
                            <span>Active (Show on website)</span>
                        </label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Quiz</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>
    
    <script>
        function openModal() {
            document.getElementById('quizModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Add Quiz';
            document.getElementById('formAction').value = 'add';
            document.getElementById('quizForm').reset();
        }
        
        function closeModal() {
            document.getElementById('quizModal').classList.remove('active');
        }
        
        function editQuiz(quiz) {
            document.getElementById('quizModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Edit Quiz';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('quizId').value = quiz.id;
            document.getElementById('question').value = quiz.question;
            document.getElementById('option_a').value = quiz.option_a;
            document.getElementById('option_b').value = quiz.option_b;
            document.getElementById('option_c').value = quiz.option_c;
            document.getElementById('option_d').value = quiz.option_d;
            document.getElementById('status').checked = quiz.is_active == 1;
            
            // Set correct option
            document.querySelectorAll('input[name="correct_option"]').forEach(radio => {
                radio.checked = radio.value === quiz.correct_option;
            });
        }
        
        function deleteQuiz(id) {
            if (confirm('Are you sure you want to delete this quiz? All votes will also be deleted.')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Close modal on outside click
        document.getElementById('quizModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
