<?php
// task.php - Todo List Application for Divyansh Arora
// This is a self-contained to-do application with PHP backend
// All tasks are stored in session for persistence during page reloads

// Start session for task storage
session_start();

// Initialize tasks array if not exists
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Initialize editing task ID if not exists
if (!isset($_SESSION['editing_task_id'])) {
    $_SESSION['editing_task_id'] = null;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_task'])) {
        // Add new task
        $newTask = [
            'id' => uniqid(),
            'text' => htmlspecialchars($_POST['new_task']),
            'completed' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
        array_unshift($_SESSION['tasks'], $newTask);
        $_SESSION['editing_task_id'] = null; // Cancel any editing when adding new task
    } elseif (isset($_POST['delete_task'])) {
        // Delete task
        $taskId = $_POST['task_id'];
        $_SESSION['tasks'] = array_filter($_SESSION['tasks'], function($task) use ($taskId) {
            return $task['id'] !== $taskId;
        });
        $_SESSION['editing_task_id'] = null; // Cancel editing if deleting
    } elseif (isset($_POST['toggle_task'])) {
        // Toggle task completion - FIXED IMPLEMENTATION
        $taskId = $_POST['task_id'];
        foreach ($_SESSION['tasks'] as &$task) {
            if ($task['id'] === $taskId) {
                // Simply flip the completion status
                $task['completed'] = !$task['completed'];
                break;
            }
        }
        $_SESSION['editing_task_id'] = null; // Cancel editing when toggling
    } elseif (isset($_POST['start_edit'])) {
        // Start editing a task
        $taskId = $_POST['task_id'];
        $_SESSION['editing_task_id'] = $taskId;
    } elseif (isset($_POST['edit_task'])) {
        // Update task text
        $taskId = $_POST['task_id'];
        $newText = htmlspecialchars($_POST['task_text']);
        foreach ($_SESSION['tasks'] as &$task) {
            if ($task['id'] === $taskId) {
                $task['text'] = $newText;
                $task['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        $_SESSION['editing_task_id'] = null; // Exit editing mode
    }
}

// Sort tasks: incomplete first, then completed
usort($_SESSION['tasks'], function($a, $b) {
    if ($a['completed'] === $b['completed']) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    }
    return $a['completed'] ? 1 : -1;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divyansh's Todo App</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Global Styles */
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc3f7;
            --dark-color: #333;
            --light-color: #f9f9f9;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

        /* Container Styles */
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .header h1 {
            color: var(--secondary-color);
            font-weight: 600;
            font-size: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header h1 i {
            color: var(--accent-color);
        }

        .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid var(--accent-color);
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Task Form Styles */
        .task-form {
            display: flex;
            margin-bottom: 30px;
            position: relative;
        }

        .task-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #ddd;
            border-radius: var(--border-radius) 0 0 var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .task-input:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 195, 247, 0.2);
        }

        .add-btn {
            padding: 0 25px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
        }

        /* Task List Styles */
        .task-list {
            list-style: none;
        }

        .task-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 12px;
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            transition: var(--transition);
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-left: 4px solid transparent;
        }

        .task-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }

        .task-item.completed {
            background-color: #f8f9fa;
            border-left-color: var(--success-color);
        }

        .task-checkbox {
            margin-right: 15px;
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: var(--success-color);
        }

        .task-text {
            flex: 1;
            font-size: 17px;
            word-break: break-word;
            padding: 5px 0;
            transition: var(--transition);
        }

        .task-text.completed {
            text-decoration: line-through;
            color: #777;
        }

        .task-actions {
            display: flex;
            gap: 8px;
        }

        .task-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 17px;
            transition: var(--transition);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .edit-btn {
            color: var(--primary-color);
        }

        .edit-btn:hover {
            background-color: rgba(74, 111, 165, 0.1);
            transform: scale(1.1);
        }

        .delete-btn {
            color: var(--danger-color);
        }

        .delete-btn:hover {
            background-color: rgba(231, 76, 60, 0.1);
            transform: scale(1.1);
        }

        .save-btn {
            color: var(--success-color);
            background-color: rgba(46, 204, 113, 0.1);
        }

        .save-btn:hover {
            background-color: rgba(46, 204, 113, 0.2);
            transform: scale(1.1);
        }

        .cancel-btn {
            color: var(--danger-color);
            background-color: rgba(231, 76, 60, 0.1);
        }

        .cancel-btn:hover {
            background-color: rgba(231, 76, 60, 0.2);
            transform: scale(1.1);
        }

        .task-edit-input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius);
            font-size: 17px;
            transition: var(--transition);
        }

        .task-edit-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 195, 247, 0.3);
        }

        .no-tasks {
            text-align: center;
            padding: 40px 20px;
            color: #777;
            background-color: #f8f9fa;
            border-radius: var(--border-radius);
            margin: 20px 0;
        }

        .no-tasks img {
            max-width: 250px;
            margin-bottom: 20px;
            opacity: 0.8;
        }

        .no-tasks h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: var(--secondary-color);
        }

        .no-tasks p {
            font-size: 16px;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Task Stats */
        .task-stats {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            color: #666;
        }

        .progress-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
            flex: 1;
            max-width: 300px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #777;
            font-size: 14px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 25px 20px;
            }
            
            .task-form {
                flex-direction: column;
            }
            
            .task-input {
                border-radius: var(--border-radius);
                margin-bottom: 10px;
                padding: 13px 15px;
            }
            
            .add-btn {
                border-radius: var(--border-radius);
                padding: 13px;
                justify-content: center;
            }
            
            .header h1 {
                font-size: 26px;
            }
            
            .avatar {
                width: 60px;
                height: 60px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .container {
                margin: 15px auto;
                padding: 20px 15px;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .task-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 18px;
            }
            
            .task-checkbox {
                margin-bottom: 10px;
            }
            
            .task-actions {
                margin-top: 15px;
                align-self: flex-end;
            }
            
            .task-stats {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .progress-bar {
                max-width: 100%;
                width: 100%;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.4s ease forwards;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-tasks"></i> Divyansh's Todo List</h1>
            <div class="avatar">
                <!-- Placeholder for user avatar -->
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="40" r="20" fill="#4a6fa5" />
                    <circle cx="50" cy="100" r="40" fill="#4a6fa5" />
                    <circle cx="40" cy="35" r="5" fill="white" />
                    <circle cx="60" cy="35" r="5" fill="white" />
                    <path d="M 35 50 Q 50 60 65 50" stroke="white" stroke-width="3" fill="none" />
                </svg>
            </div>
        </div>
        
        <form method="POST" class="task-form">
            <input type="text" name="new_task" class="task-input" placeholder="What needs to be done?" required>
            <button type="submit" name="add_task" class="add-btn">
                <i class="fas fa-plus"></i> Add Task
            </button>
        </form>
        
        <?php if (empty($_SESSION['tasks'])): ?>
            <div class="no-tasks fade-in">
                <!-- Empty state illustration -->
                <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                    <rect x="50" y="50" width="300" height="200" rx="10" fill="#f0f4f8" stroke="#4a6fa5" stroke-width="2" stroke-dasharray="8 4" />
                    <line x1="70" y1="100" x2="330" y2="100" stroke="#4a6fa5" stroke-width="2" stroke-dasharray="8 4" />
                    <line x1="70" y1="150" x2="330" y2="150" stroke="#4a6fa5" stroke-width="2" stroke-dasharray="8 4" />
                    <line x1="70" y1="200" x2="330" y2="200" stroke="#4a6fa5" stroke-width="2" stroke-dasharray="8 4" />
                    <circle cx="100" cy="75" r="15" fill="#4fc3f7" opacity="0.6" />
                    <circle cx="100" cy="125" r="15" fill="#4fc3f7" opacity="0.4" />
                    <circle cx="100" cy="175" r="15" fill="#4fc3f7" opacity="0.2" />
                    <text x="200" y="260" font-family="Arial" font-size="24" fill="#4a6fa5" text-anchor="middle">No tasks yet!</text>
                </svg>
                <h3>Your task list is empty</h3>
                <p>Add your first task above to get started on your productivity journey.</p>
            </div>
        <?php else: ?>
            <ul class="task-list">
                <?php foreach ($_SESSION['tasks'] as $index => $task): ?>
                    <?php $isEditing = ($task['id'] === $_SESSION['editing_task_id']); ?>
                    <li class="task-item fade-in <?= $task['completed'] ? 'completed' : '' ?>" style="animation-delay: <?= $index * 0.05 ?>s">
                        <form method="POST" style="display: flex; width: 100%; align-items: center;">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            
                            <!-- Toggle task completion - FIXED IMPLEMENTATION -->
                            <input 
                                type="checkbox" 
                                class="task-checkbox" 
                                name="toggle_task" 
                                <?= $task['completed'] ? 'checked' : '' ?> 
                                onchange="this.form.submit()"
                            >
                            
                            <?php if ($isEditing): ?>
                                <!-- Edit task form -->
                                <input 
                                    type="text" 
                                    name="task_text" 
                                    class="task-edit-input" 
                                    value="<?= $task['text'] ?>" 
                                    autofocus
                                    required
                                >
                                <div class="task-actions">
                                    <button type="submit" name="edit_task" class="task-btn save-btn" title="Save">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="task-btn cancel-btn" title="Cancel" onclick="location.reload()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <!-- Display task text -->
                                <span class="task-text <?= $task['completed'] ? 'completed' : '' ?>">
                                    <?= $task['text'] ?>
                                </span>
                                <div class="task-actions">
                                    <button type="submit" name="start_edit" class="task-btn edit-btn" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="submit" name="delete_task" class="task-btn delete-btn" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <div class="task-stats">
                <?php 
                $totalTasks = count($_SESSION['tasks']);
                $completedTasks = count(array_filter($_SESSION['tasks'], function($task) { 
                    return $task['completed']; 
                }));
                $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                ?>
                <div>
                    <p><strong><?= $completedTasks ?></strong> of <strong><?= $totalTasks ?></strong> tasks completed</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $completionPercentage ?>%"></div>
                    </div>
                </div>
                <div>
                    <p>Completion: <strong><?= $completionPercentage ?>%</strong></p>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="footer">
            <p>Created by Divyansh Arora | <?= date('Y') ?></p>
        </div>
    </div>
    
    <!-- JavaScript for enhanced interactivity -->
    <script>
        // Focus on input field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const inputField = document.querySelector('.task-input');
            if (inputField) {
                inputField.focus();
            }
            
            // Smooth animations for task items
            const taskItems = document.querySelectorAll('.task-item');
            taskItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
            });
        });
    </script>
</body>
</html>