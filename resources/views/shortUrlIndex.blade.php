<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <title>短链,短网址,长链转短链</title>
    <link rel="stylesheet" href="/css/index.css">
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="/js/clipboard.min.js"></script>
    <script type="text/javascript" src="/js/index.js" charset="UTF-8"></script>
</head>
<body>
<div class="s-create">
    <!--生成短链部分-->
    <input type="text" class="l-url" id="lUrl" placeholder="网址以 http:// 或 https:// 开头">
    <input type="submit" class="l-btn" id="lBtn" value="生成短链">
    <!-- 短链展示 -->
    <div class="s-result">
        <span class="s-url" id="sUrl">http://2dw.win/url</span>
        <button class="copy-s-url copy" id="copySUrl" data-clipboard-text="http://2dw.win/s/url">复制短链</button>
    </div>
    <!-- 作者 -->
    <div class="author">
        <a href="http://51growup.com" target="_blank">51growup.com-朱明振</a>
        <a href="javascript:" style="display: block;">© 2017 2dw.win V1.1</a>
    </div>
</div>
</body>
</html>

