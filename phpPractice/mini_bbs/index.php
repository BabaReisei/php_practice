<?php
    // セッション開始
    session_start();

    // DAO取得
    require('dbconnect.php');

    // セッションにIDが設定してあり、かつ、セッションタイムアウトをしていない場合
    if (isset($_SESSION['id'])
            && $_SESSION['time'] + 3600 > time()) {

        // セッションタイムアウト時間更新
        $_SESSION['time'] = time();

        // DB検索
        $members = $db -> prepare('SELECT * 
                                   FROM members
                                   WHERE id=?'); 
        $members -> execute(array($_SESSION['id']));
        $member = $members -> fetch();

    // 上記以外の場合はログイン未完了のためログイン画面へ遷移する。
    } else {
        header('Location:login.php');
        exit();
    }

    // POST（リクエスト）が送信された場合
    if (!empty($_POST)) {
        
        // メッセージが空ではない場合
        if ($_POST['message'] !== '') {

            // $_POST['reply_post_id']が空の場合登録ができないため、デフォルト値を設定しておく
            $reply_post_id = '0';

            // $_POST['reply_post_id']に設定値がある場合は、$_POST['reply_post_id']を設定する。
            if ($_POST['reply_post_id'] !== '') {
                $reply_post_id = $_POST['reply_post_id'];
            }
            // DB登録
            $message = $db -> prepare('INSERT 
                                       INTO posts
                                       SET member_id=?,
                                           message=?,
                                           reply_message_id=?,
                                           created=NOW()');
            $message -> execute(array($member['id'], $_POST['message'], $reply_post_id));
        }

        // 2重押下防止のためindex.phpへ遷移
        header('Location:index.php');
        exit();
    }

    // ページの計算
    $page = $_REQUEST['page'];

    // ページのデフォルト値設定
    if ($page == '') {
      $page = 1;
    }

    // 最大ページの取得
    $counts = $db -> query('SELECT COUNT(*) AS cnt 
                            FROM posts');
    $cnt = $counts -> fetch();
    $maxPage = ceil($cnt['cnt'] / 5);

    // ページの最大・最少を設定
    $page = max($page, 1);
    $page = min($page, $maxPage);
    $start = ($page - 1) * 5;

    // 一覧表示のため、メッセージを取得
    $posts = $db -> prepare('SELECT m.name,
                                    m.picture,
                                    p.* 
                             FROM members m,
                                  posts p
                             WHERE m.id=p.member_id
                             ORDER BY p.created DESC
                             LIMIT ?, 5');
    $posts -> bindParam(1, $start, PDO::PARAM_INT);
    $posts -> execute();

    // $_REQUEST['res']に設定値がある場合（返信時）
    if (isset($_REQUEST['res'])) {

        // DB検索
        $response = $db -> prepare('SELECT m.name,
                                           m.picture,
                                           p.*
                                    FROM members m,
                                         posts p
                                    WHERE m.id=p.member_id
                                      AND p.id=?');
        $response -> execute(array($_REQUEST['res']));
        $table = $response -> fetch();
        $message = '@'. $table['name']. ' '. $table['message'];
    }
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <meta http-equiv="X-UA-Compatible" content="ie=edge">
	  <title>ひとこと掲示板</title>
	  <link rel="stylesheet" href="style.css" />
  </head>

  <body>
    <div id="wrap">
      <div id="head">
        <h1>ひとこと掲示板</h1>
      </div>
      <div id="content">
  	    <div style="text-align: right">
          <a href="logout.php">ログアウト</a>
        </div>
        <form action="" method="post">
          <dl>
            <dt><?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>さん、メッセージをどうぞ</dt>
            <dd>
              <textarea name="message" cols="50" rows="5"><?php print(htmlspecialchars($message, ENT_QUOTES)); ?></textarea>
              <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)) ?>" />
            </dd>
          </dl>
          <div>
            <p>
              <input type="submit" value="投稿する" />
            </p>
          </div>
        </form>
        <?php foreach ($posts as $post): ?>
            <div class="msg">
              <img src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>"
                      width="48" height="48" alt="<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" />
              <p>
                <?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?>
                <span class="name">（<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>）</span>
                [<a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>">Re</a>]
              </p>
              <p class="day">
                <a href="view.php?id=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>">
                        <?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></a>
                <?php if ($post['reply_message_id'] > 0): ?>
                    <a href="view.php?id=<?php print(htmlspecialchars($post['reply_message_id'])) ?>">
                            返信元のメッセージ</a>
                <?php endif; ?>
                <?php if ($_SESSION['id'] == $post['member_id']): ?>
                    [<a href="delete.php?id=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>"
                            style="color: #F33;">削除</a>]
                <?php endif; ?>
              </p>
            </div>
        <?php endforeach; ?>
        <ul class="paging">
          <?php if ($page > 1): ?>
              <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
          <?php else: ?>
            <li>前のページへ</li>
          <?php endif; ?>
          <?php if ($page < $maxPage): ?>
              <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
          <?php else: ?>
              <li>次のページへ</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </body>
</html>
