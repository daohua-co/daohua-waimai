import { test, expect } from 'vitest';
import { FormActions, ListStore } from '../src/lib';

test('test FormAction', async () => {
  const store = new ListStore('/api.json?');
  const actions = new FormActions(store, null, {name: '', realname: '', is_super: false});
  const { item } = actions;
  // 打开创建
  actions.create();
  expect(item.id).toBeUndefined();
  expect(item.name).toBe('');
  expect(item.is_super).toBe(false);
  item.sex = 2;
  // 打开编辑
  await actions.edit(1).then(async resp => {
    // console.log(actions.item);
    expect(item.id).toBeGreaterThan(0);
    expect(item.name).toBe('admin');
    expect(item.realname).toBe('Administrator');
    expect(item.sex).toBe(0);
    expect(item.is_super).toBe(true);
    // 重置
    item.sex = 1;
    expect(item.sex).toBe(1);
    actions.reset();
    expect(item.sex).toBe(0);

    // test save reset
    item.email = 'my@a.com';
    await actions.save();
    item.email = 'xxx';
    expect(item.email).toBe('xxx');
    actions.reset();
    expect(item.email).toBe('my@a.com');

    // 再创建，数据为来自暂存（item.sex）
    actions.create();
    expect(item.id).toBeUndefined();
    expect(item.name).toBe('');
    expect(item.sex).toBe(2);
    // 创建保存时，恢复默认数据
    await actions.save();
    expect(item.sex).toBeUndefined();
    expect(item.is_super).toBe(false);
    // 创建切换到编辑，数据暂存
    item.name = 'test';
    item.sex = 1;
    await actions.edit(2).then(() => {
      expect(item.id).toBeGreaterThan(0);
      // 检测被暂存数据
      actions.create();
      expect(item.id).toBeUndefined();
      expect(item.name).toBe('test');
      expect(item.sex).toBe(1);
    });
  });
});
