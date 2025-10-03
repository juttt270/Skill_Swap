<?php

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap | Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f8f9fa; }
        .chat-container { display:flex; height:100vh; }
        .chat-sidebar { width:25%; background:#fff; border-right:1px solid #ddd; padding:15px; overflow-y:auto;}
        .chat-main { flex:1; display:flex; flex-direction:column;}
        .chat-header { padding:15px; background:#fff; border-bottom:1px solid #ddd; }
        .chat-box { flex:1; padding:15px; overflow-y:auto; background:#f1f3f6; scrollbar-width: none;}
        .chat-message { display:flex; align-items:flex-end; margin-bottom:15px; }
        .chat-message.me { flex-direction: row-reverse; text-align:right; }
        .chat-message .msg-content { padding:10px 15px; border-radius:15px; max-width:70%; }
        .chat-message.me .msg-content { background:#5FCF80; color:#fff; }
        .chat-message.other .msg-content { background:#e9ecef; color:#000; }
        .chat-message img { width:40px; height:40px; border-radius:50%; margin:0 10px; object-fit:cover; }
        .chat-footer { background:#fff; padding:10px; border-top:1px solid #ddd; }
        .reply-info { font-size:12px; color:#6c757d; margin-bottom:5px; }
        .status { font-size:11px; color:#888; margin-top:3px; }
    </style>
</head>
<body>
<div class="chat-container">
    <!-- LEFT SIDEBAR -->
    <div class="chat-sidebar">
        <h5 class="mb-3">My Swaps</h5>
        <ul class="list-group">
            <?php while ($s = mysqli_fetch_assoc($swaps)): ?>
                <li style="background-color:#5FCF80 !important;" 
                    class="list-group-item <?= ($s['id'] == $swap_id) ? 'active' : '' ?>">
                    <a style="color:white !important;" 
                       href="?chat&swap_id=<?= $s['id'] ?>" 
                       class="text-decoration-none <?= ($s['id'] == $swap_id) ? 'text-white' : '' ?>">
                        Swap #<?= $s['id'] ?> - <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['category']) ?>)
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- MAIN CHAT -->
    <div class="chat-main">
        <?php if ($swap_id > 0): ?>
            <!-- HEADER -->
            <div class="chat-header">
                <h5 class="mb-0">Chat (Swap #<?= $swap_id ?>)</h5>
            </div>

            <!-- CHAT MESSAGES -->
            <div class="chat-box">
                <?php while ($m = mysqli_fetch_assoc($messages)): ?>
                    <div class="chat-message <?= ($m['sender_id'] == $user_id) ? 'me' : 'other' ?>">
                        <?php 
                            $pic = $m['profile_picture'] ? '../Skill_Swap/'.$m['profile_picture'] : 'https://via.placeholder.com/40';
                        ?>
                        <img src="<?= $pic ?>" alt="pic">
                        <div>
                            <?php if ($m['parent_id']): ?>
                                <?php
                                    $reply_sql = "SELECT message FROM messages WHERE id = {$m['parent_id']} LIMIT 1";
                                    $reply_res = mysqli_query($connection, $reply_sql);
                                    $reply_txt = '';
                                    if ($r = mysqli_fetch_assoc($reply_res)) {
                                        $reply_txt = mb_substr($r['message'], 0, 30) . (strlen($r['message']) > 30 ? '...' : '');
                                    }
                                ?>
                                <div class="reply-info">↳ <?= htmlspecialchars($reply_txt) ?></div>
                            <?php endif; ?>
                            <div class="msg-content">
                                <b><?= htmlspecialchars($m['sender_name']) ?>:</b> <?= htmlspecialchars($m['message']) ?>
                                <div class="small text-muted"><?= $m['created_at'] ?></div>
                                <?php if ($m['sender_id'] == $user_id): ?>
                                    <div class="status"><?= $m['is_read'] ? "✓✓ Seen" : "✓ Sent" ?></div>
                                <?php endif; ?>
                                <a href="public.php?chat&swap_id=<?= $swap_id ?>&reply_to=<?= $m['id'] ?>" class="small">Reply</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- MESSAGE FORM -->
            <div class="chat-footer">
                <form method="post" class="d-flex gap-2 flex-column">
                    <?php if ($parent_id > 0): ?>
                        <input type="hidden" name="parent_id" value="<?= $parent_id ?>">
                        <p class="text-muted">Replying to: "<?= htmlspecialchars($parent_text) ?>"</p>
                    <?php endif; ?>
                    <div class="d-flex gap-2">
                        <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                        <button type="submit" name="send" class="btn" style="background-color:#5FCF80; color:white;">Send</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="d-flex align-items-center justify-content-center flex-grow-1">
                <h4>Select a swap to start chat</h4>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>