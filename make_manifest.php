<?php
/**
 * Git pre-commit 훅 전용 manifest.json 생성기
 * 위버코어 + 모든 플러그인 manifest.json을 한 번에 갱신
 */

$repo   = "sektis/weaver-all"; // GitHub 저장소
$branch = "master";            // 브랜치 이름
$base   = __DIR__;             // /plugin/weaver 기준

function make_raw_link($repo, $branch, $path){
    return "https://raw.githubusercontent.com/{$repo}/{$branch}/plugin/weaver/{$path}";
}

function build_manifest($target_base, $rel_prefix, $repo, $branch){
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target_base));
    $list = array();

    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        $rel = ltrim(str_replace($target_base, '', $file->getPathname()), DIRECTORY_SEPARATOR);

        // 무시할 파일
        if ($rel === 'manifest.json') continue;
        if ($rel === 'make_manifest.php') continue;
        if (strpos($rel, '.git'.DIRECTORY_SEPARATOR) === 0) continue;

        $path = ($rel_prefix ? $rel_prefix.'/' : '').str_replace(DIRECTORY_SEPARATOR, '/', $rel);
        $list[$path] = make_raw_link($repo, $branch, $path);
    }
    return $list;
}

// 🔹 위버코어 manifest.json
$core_files = build_manifest($base, '', $repo, $branch);
foreach (array_keys($core_files) as $path){
    if (strpos($path, 'plugins/') === 0) unset($core_files[$path]);
}
file_put_contents(
    $base.'/manifest.json',
    json_encode($core_files, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
);
echo "[core] manifest.json 생성 (" . count($core_files) . " files)\n";

// 🔹 각 플러그인 manifest.json
$plugins_dir = $base.'/plugins';
if (is_dir($plugins_dir)) {
    foreach (glob($plugins_dir.'/*', GLOB_ONLYDIR) as $plugin_path) {
        $plugin_name  = basename($plugin_path);
        $plugin_files = build_manifest($plugin_path, "plugins/{$plugin_name}", $repo, $branch);

        file_put_contents(
            $plugin_path.'/manifest.json',
            json_encode($plugin_files, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
        );
        echo "[plugin:{$plugin_name}] manifest.json 생성 (" . count($plugin_files) . " files)\n";
    }
}
