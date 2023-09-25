<template>
  <el-form ref="formRef" :model="item" label-width="100px" class="show-wp">
    <el-drawer v-model="is.show" title="商品详情" size="720">
      <el-skeleton :rows="10" :loading="is.loading"/>
      <div v-if="!is.loading">
        <div>
          <el-descriptions title="基本信息" :column="1" label-align="right" label-size="small" border>
            <el-descriptions-item label="商品编码">{{ item.code ?? '' }}</el-descriptions-item>
            <el-descriptions-item label="商品标题">{{ item.title }}</el-descriptions-item>
            <el-descriptions-item label="商品卖点">{{ item.ups ?? '-' }}</el-descriptions-item>
            <el-descriptions-item label="价格">{{ item.price }}（元）</el-descriptions-item>
            <el-descriptions-item label="打包费">{{ item.packing_fee }}（元）</el-descriptions-item>
            <el-descriptions-item label="显示排序">{{ item.asc_num }} <span class="tips">从小到大显示</span>
            </el-descriptions-item>

            <el-descriptions-item label="销售状态">
              <el-tag size="small" effect="plain" :type="item.is_on_sale ? 'success' : 'danger'" round>
                {{ item.is_on_sale ? '在售' : '停售' }}
              </el-tag>
            </el-descriptions-item>

            <el-descriptions-item label="分类">
              <el-tag class="mr-2" v-for="cat in item.categories" :key="cat.id">{{ cat.title }}</el-tag>
            </el-descriptions-item>

            <el-descriptions-item label="标签">
              <el-tag class="mr-2" v-for="tag in item.tags" :key="tag.id">{{ tag.title }}</el-tag>
            </el-descriptions-item>

            <el-descriptions-item label="商品相册">
              <el-image
                class="w-24 h-24 mr-2"
                fit="cover"
                :preview-src-list="item.album"
                :src="img"
                :key="img"
                v-for="(img, index) in item.album"
                :initial-index="index"
                :hide-on-click-modal="true"
              />
            </el-descriptions-item>
            <el-descriptions-item label="创建时间">{{ item.created_at }}
            </el-descriptions-item>
            <el-descriptions-item label="更新时间">{{ item.updated_at }}
            </el-descriptions-item>
            <el-descriptions-item label="删除时间">
              {{ item.deleted_at || '-' }}
            </el-descriptions-item>
          </el-descriptions>
        </div>

        <el-descriptions class="mt-6" title="商品规格" :column="1" label-align="right" label-size="small" border>
          <el-descriptions-item label="规格选项">
            <div v-if="!item.has_sku">无</div>
            <div class="my-2" v-for="skuOption in item.skus_formatted.options" :key="skuOption.name_id">
              <b>{{ nameTitles[skuOption.name_id] }}：</b>
              <el-tag class="mr-2" v-for="valueId in skuOption.value_ids" :key="valueId">
                {{ valueTitles[valueId] }}
              </el-tag>
            </div>
          </el-descriptions-item>
          <el-descriptions-item label="规格明细" v-if="item.has_sku">
            TODO
          </el-descriptions-item>
        </el-descriptions>

        <el-descriptions class="mt-6" :column="1" title="商品介绍" label-align="right" label-size="small" border>
          <el-descriptions-item label="商品简介">{{ item.intro ?? '-' }}</el-descriptions-item>
          <el-descriptions-item label="详细介绍">
            <div class="detail-preview" v-html="item.detail"></div>
          </el-descriptions-item>
        </el-descriptions>

      </div>
      <template #footer>
        <el-button type="primary" @click="() => showActions.toEdit($parent.$refs.formRef)">编辑</el-button>
      </template>
    </el-drawer>
  </el-form>
</template>

<script setup>
import {reactive} from 'vue';
import {goodsStore} from '/src/admin/stores';
import {refKit, ShowActions} from '/src/lib';

const showActions = new ShowActions(goodsStore.api);
const {
  item,
  is,
  show,
} = showActions;

const nameTitles = reactive({});
const valueTitles = reactive({});

defineExpose({
  show: id => {
    show(id).then((resp) => {
      refKit.empty(valueTitles);
      refKit.empty(nameTitles);
      item.sku_names.forEach(item => {
        nameTitles[item.id] = item.title;
      });
      item.sku_values.forEach(item => {
        valueTitles[item.id] = item.title;
      });
    });
  }
});
</script>

<style lang="scss">
.show-permissions .el-tree-node__content {
  display: none;
}

.show-permissions .is-indeterminate > .el-tree-node__content,
.show-permissions .is-checked .el-tree-node__content {
  display: flex;
}
</style>
