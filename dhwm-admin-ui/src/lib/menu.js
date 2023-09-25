
let _menuMap;

/**
 * 构建菜单选中状态索引结构
 * @param menu
 * @return {{
 *   paths: {$path: [$idx0, $idx1]},
 *   modules: {$module: $idx0}
 * }}
 */
const menuMap = menu => {
  if (_menuMap) {
    return _menuMap;
  }

  const paths = {};
  const modules = {};
  for (const idx0 in menu) {
    const children = menu[idx0].children;
    if (menu[idx0].module) {
      modules[menu[idx0].module] = parseInt(idx0); // modules[$module] = $idx0;
    }
    for (const idx1 in children) {
      const item = children[idx1];
      paths[item.path] = [parseInt(idx0), parseInt(idx1)]; // path[$path] = [$idx0, $idx1];
    }
  }
  _menuMap = { paths, modules };
  return _menuMap;
};

export const getMenuActive = (path, menu) => {
  const menuActiveMap = menuMap(menu);
  const active = menuActiveMap.paths[path];

  if (active) {
    // menuActiveMap.paths[path] 存在
    return active;
  }

  if (path.match(/^\/[\w-]+$/)) {
    return [-1, -1];
  }

  // 二级菜单子页面
  const pathNodes = path.replace(/^\/([\w-/]+)(\/.*)/, '$1').split('/'); // 取 ?/# 前的部分，用 '/' 拆分成数组
  let childPageIndex = null;
  // 从后尾开始，逐次去掉一段 /xxx，取得 subPath，在 menuActiveMap.paths[$subPath] 中查找 subPath
  for (let i = pathNodes.length; i > 0; i--) {
    childPageIndex = menuActiveMap.paths['/' + pathNodes.join('/')];
    if (childPageIndex) {
      return childPageIndex;
    }
    pathNodes.pop();
  }

  // 不在菜单中，找顶级菜单
  const module = path.replace(/^\/([\w-]+).*/, '$1');
  const moduleIndex = menuActiveMap.modules[module];
  if (moduleIndex) {
    return [moduleIndex, -1];
  }

  // 顶级菜单中也没有
  return [-1, -1];
};
