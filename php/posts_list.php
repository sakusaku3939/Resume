<?php

$post_data = [];
foreach (getFilename('../posts/') as $folder) {
    if ($folder !== 'style.css') {
        $parameter = _parameter($folder);
        $path = '../posts/' . $folder . '/' . $parameter . '.html';
        $content = file_get_contents($path);
        $title = _title($content);
        $overview = _overview($content);
        $position = _position($content);
        $image_path = _image($content, $folder);

        $post_data[] = [
            'title' => $title,
            'overview' => $overview,
            'position' => $position,
            'image' => $image_path,
            'parameter' => $parameter,
            'date' => $folder
        ];
    }
}

$json = json_encode($post_data, JSON_UNESCAPED_UNICODE);
echo '<div class="card-contents">';
foreach ($post_data as $post) {
    echo '<div class="card card-skin" onclick="click_posts(\'' . $post["date"] . '\', \'' . $post["parameter"] . '\')">' .
        '<div class="card_date">' . date('Y.m.d', strtotime($post['date'])) . '</div>' .
        '<div class="card_imgframe"
         style="background-image: url(' . $post['image'] . '); background-position: ' . $post['position'] . ';
         "></div>' .
        '<div class="card_textbox">' .
        '<div class="card_titletext">' . $post['title'] . '</div>' .
        '<div class="card_overviewtext">' . $post['overview'] . '</div>' .
        '</div></div>';
}
echo '</div>';

function getFilename($directory)
{
    return array_diff(scandir($directory, 1), array('.', '..'));
}

function _title($content)
{
    if ($content and preg_match('!<h1>(.*?)</h1>!s', $content, $title)) {
        return $title[1];
    }
    return 'no title';
}

function _overview($content)
{
    if ($content and preg_match('!<div id="overview">(.*?)</div>!s', $content, $overview)) {
        return $overview[1];
    }
    return 'no overview';
}

function _position($content)
{
    if ($content and preg_match('!<div id="position">(.*?)\s(.*?)</div>!s', $content, $overview)) {
        return $overview[1] . ' ' . $overview[2];
    }
    return 'center center';
}

function _image($content, $folder)
{
    if ($content and preg_match('!<img src="(.*?)"!s', $content, $image_path)) {
        return '../posts/' . $folder . '/' . $image_path[1];
    }
    return '../image/NoImage.jpg';
}

function _parameter($folder)
{
    foreach (getFilename('../posts/' . $folder) as $name) {
        if (strpos($name, '.html')) {
            return preg_replace("/(.+)(\.[^.]+$)/", "$1", $name);
        }
    }
    return 'no-parameter';
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/posts_list.css" type="text/css">
    <!-- フォント -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@600&display=swap" rel="stylesheet">
    <title></title>
</head>
<body>
<script>
    window.parent.setPost_data(<?php echo $json?>)

    function click_posts(date, parameter) {
        if (!window.parent.getPost_click()) {
            window.parent.setPost_click(true)
            window.parent.sessionStorage.setItem("src", "posts/" + date + "/" + parameter + ".html")
            window.parent.history.pushState(null, null, "?posts=" + parameter)
            window.parent.posts_before_loading()
        }
    }

    window.onresize = function () {
        const card = document.getElementsByClassName('card-contents')[0]
        if (navigator.userAgent.match(/(iPhone|iPad|iPod|Android)/i)) {
            card.style.marginBottom = '96px'
        } else {
            card.style.marginBottom = '24px'
        }
    }
</script>
</body>
</html>