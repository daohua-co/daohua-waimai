<template>
  <el-form-item label="规格参数">
    <div class="goods-skus p-3 w-full border border-gray-200 border-solid rounded">
      <div class="sku-wp w-full">
        <Draggable v-model="rawSkuOptions" item-key="name_id">
          <template #item="{element, index}">
            <div class="sku-item border border-gray-100 border-solid mb-3 relative">
              <el-link class="sku-rm">
                <ConfirmOperate title="您确定要删除该规格吗？" type="icon" @confirm="removeSku(index)"/>
              </el-link>
              <el-form-item class="px-3 py-1 bg-gray-100" label="规格名：" label-width="70">
                <el-select
                  v-model="rawSkuOptions[index].name_id"
                  allow-create
                  class="w-36 mr-2"
                  clearable
                  default-first-option
                  filterable
                  placeholder="请选择或输入"
                  @change="onSkuChange(element, index)"
                >
                  <template v-for="nameItem in skuRenderData.nameItems" :key="nameItem.id">
                    <el-option
                      v-if="nameItem.id === rawSkuOptions[index].name_id || !rawSkuOptions.map(item => item.name_id).includes(nameItem.id)"
                      :label="nameItem.title"
                      :value="nameItem.id"
                    >
                    </el-option>
                  </template>
                </el-select>
                <el-checkbox v-model="element.enable_image"><span class="txt-normal">添加规格图片</span>
                </el-checkbox>
                <el-checkbox v-if="element.enable_image" v-model="element.show_image">
                  <span class="txt-normal">购买时选择规格显示为规格图片</span>
                </el-checkbox>
              </el-form-item>
              <el-form-item class="px-3 py-2" label="规格值：" label-width="70">
                <div
                  v-for="(useValue, valueIndex) in element.value_ids"
                  :key="valueIndex"
                  class="sku-value-item w-24 mr-3 my-1.5 relative"
                >
                  <el-link class="sku-rm">
                    <ConfirmOperate title="您确定要删除该规格值吗？" type="icon" @confirm="removeSkuValue(index, valueIndex)"/>
                  </el-link>
                  <el-select
                    v-model="rawSkuOptions[index].value_ids[valueIndex]"
                    :loading="!skuRenderData.valuesLoaded[element.name_id]"
                    allow-create
                    clearable
                    default-first-option
                    filterable
                    placeholder=""
                    @change="onSkuValueChange(index, valueIndex)"
                  >
                    <template v-for="skuValue in skuRenderData.valueItems[element.name_id]" :key="skuValue.id">
                      <el-option
                        v-if="skuValue.id === element.value_ids[valueIndex] || !element.value_ids.includes(skuValue.id)"
                        :label="skuValue.title"
                        :value="skuValue.id">
                      </el-option>
                    </template>
                  </el-select>
                  <div v-if="rawSkuOptions[index].enable_image" class="sku-value-image-wp w-24 h-24">
                    <UploadImage v-model="rawSkuOptions[index].value_images[useValue]" :disabled="!useValue"/>
                  </div>
                </div>
                <el-link v-if="element.name_id" class="add-sku-value-btn" type="primary" @click.prevent="addSkuValue(index)">
                  + 添加规格值
                </el-link>
                <span v-if="!element.name_id" class="tips">请先添加规格名</span>
              </el-form-item>
            </div>
          </template>
        </Draggable>
      </div>
      <div class="sku-add py-1">
        <el-button :disabled="rawSkuOptions.length >= 3" @click.prevent="addSku">+ 添加规格</el-button>
        <span class="tips">上下拖动规格可排序。输入后回车可新建规格/规格值</span>
      </div>
    </div>
  </el-form-item>

  <el-form-item v-if="skuRenderData.skuList.length > 0" label="规格明细">
    <div class="sku-detail-wp w-full margin-large-bottom">
      <el-table
        :class="'w-full sku-' + skuData.options.length"
        :data="skuRenderData.skuList"
        :span-method="skuSpanMethod"
        border
      >
        <el-table-column class-name="value-column-0" prop="valueLabel0">
          <template #header>
            {{ skuColumnTitle(0) }}
          </template>
        </el-table-column>
        <el-table-column v-if="skuData.options.length > 1" class-name="value-column-1" prop="valueLabel1">
          <template #header>
            {{ skuColumnTitle(1) }}
          </template>
        </el-table-column>
        <el-table-column v-if="skuData.options.length > 2" class-name="value-column-2" prop="valueLabel2">
          <template #header>
            {{ skuColumnTitle(2) }}
          </template>
        </el-table-column>
        <el-table-column align="center" label="价格（元）" width="140">
          <template #default="{row}">
            <el-form-item todo="前端验证">
              <el-input-number v-model.number="skuData.skuMap[row.skuKey].price" :min="0.00" :precision="2" :step="0.01" size="small"></el-input-number>
            </el-form-item>
          </template>
        </el-table-column>
        <el-table-column align="center" label="规格编码" width="120">
          <template #default="{row}">
            <el-input v-model="skuData.skuMap[row.skuKey].code" size="small"></el-input>
          </template>
        </el-table-column>
      </el-table>
      <div class="table-footer">
        <el-link type="primary" @click="setSkuPriceBat()">批量设置价格</el-link>
      </div>
    </div>
  </el-form-item>

  <!----
  <div class="p-5">
      skuData:
      <pre>{{ skuData }}</pre>
  </div>
  <div class="p-5">
      rawSkuOptions:
      <pre>{{ rawSkuOptions }}</pre>
  </div>
  <div class="p-5">
      skuData:
      <pre>{{ skuData }}</pre>
  </div>
  ---->
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import Draggable from 'vuedraggable';
import UploadImage from '/src/components/UploadImage.vue';
import { prompt, refKit, toast } from '/src/lib';
import { goodsSkuNameStore, goodsSkuValueStore } from '/src/admin/stores';

const props = defineProps(['modelValue', 'formRef', 'price']);
const emit = defineEmits(['update:modelValue', 'update:price']);
// skuData.value = { options: [], skuMap: {$skuKey: $skuItem, ...}}
const skuData = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});
const price = computed({
  get: () => props.price,
  set: (value) => emit('update:price', value)
});

const rawSkuOptions = ref([]);

const skuMapData = {
  nameTitleMap: {}, // { $nameId: $title }
  valueTitleMaps: {} // { $valueId: $valueTitle, ...}
};

const skuRenderData = reactive({
  nameItems: [], // [{id: $id, title: $title}, ...]
  valueItems: {}, // { $nameId => [{id: $id, title: $title}, ...]}
  valuesLoaded: {}, // { $nameId: true, ... }
  skuList: [] // 规格组合明细
});

// 加载已创建的规格名
goodsSkuNameStore.api.fetchItems().then(resp => {
  skuRenderData.nameItems = resp.items;
  resp.items.forEach(item => {
    skuMapData.nameTitleMap[item.id] = item.title;
  });
});

// 添加规格
const addSku = () => {
  if (rawSkuOptions.value.length >= 3) {
    return;
  }
  rawSkuOptions.value.push({
    name_id: null,
    value_ids: [],
    value_images: {}, // {$useValueId: $imagePath}
    enable_image: false,
    show_image: true
  });// 规范：表字段用下划线，变量名用驼峰式
};

// 删除规格
const removeSku = (skuIndex) => {
  rawSkuOptions.value.splice(skuIndex, 1);
};

const fetchSkuValueItems = nameId => {
  return goodsSkuValueStore.api.fetchItems({name_id: nameId}).then(resp => {
    skuRenderData.valueItems[nameId] = resp.items;
    resp.items.forEach(item => {
      skuMapData.valueTitleMaps[item.id] = item.title;
    });
    skuRenderData.valuesLoaded[nameId] = true;
    return resp;
  });
};

// 选规格名/清空已选规格名
const onSkuChange = (sku, skuIndex) => {
  sku.value_ids = sku.name_id ? [null] : [];
  rawSkuOptions.value[skuIndex] = sku;

  if (sku.name_id === '') {
    // 清空已选规格名
    return;
  }

  // 规格不能重复
  const ids = rawSkuOptions.value.map(item => item.name_id);
  if (ids.indexOf(sku.name_id) !== ids.lastIndexOf(sku.name_id)) {
    rawSkuOptions.value[skuIndex].name_id = '';
    rawSkuOptions.value[skuIndex].value_ids = [];
    toast('该规格已存在');
    return;
  }

  // 新建规格，不需查询规格值
  if (!skuMapData.nameTitleMap[sku.name_id]) {
    skuRenderData.valueItems[sku.name_id] = {};
    skuRenderData.valuesLoaded[sku.name_id] = true;
    return;
  }

  // 选择规格，确保加载规格值选项
  if (skuRenderData.valuesLoaded[sku.name_id]) {
    return;
  }
  fetchSkuValueItems(sku.name_id);
};

// 添加一个待选规格值
const addSkuValue = (skuIndex) => {
  rawSkuOptions.value[skuIndex].value_ids.push(null);
};

// 删除规格值
const removeSkuValue = (skuIndex, valueIndex) => {
  rawSkuOptions.value[skuIndex].value_ids.splice(valueIndex, 1);
};

// 规格值变化时触发
const onSkuValueChange = (skuIndex, valueIndex) => {
  // 不能重复输入规格值
  const valueIds = rawSkuOptions.value[skuIndex].value_ids;
  const valueId = valueIds[valueIndex];
  if (valueId && valueIds.indexOf(valueId) !== valueIds.lastIndexOf(valueId)) {
    toast('该规格值已存在');
    rawSkuOptions.value[skuIndex].value_ids[valueIndex] = null;
  }
};

const setSkuPriceBat = () => {
  // if (props.formRef) {
  //     props.formRef.clearValidate();
  // }
  prompt({
    title: '批量设置规格价格',
    tip: '请输入价格',
    inputPattern: /[0-9.]+/,
    inputErrorMessage: '请输入正确的价格'
  }).then(result => {
    const price = parseFloat(result.value);
    for (const skuKey in skuData.value.skuMap) {
      skuData.value.skuMap[skuKey].price = price;
    }
  });
};

// 计算规格明细列的行数和列数
const skuSpanMethod = ({
  rowIndex,
  columnIndex
}) => {
  const skuOptions = skuData.value.options;
  // 规格个数
  const skuLen = skuOptions.length;

  // 有2个规格时，第1列需要合并行；
  // 有3个规格时，第1、2列需要合并行。
  if ((skuLen === 2 && columnIndex === 0) || (skuLen === 3 && (columnIndex === 0 || columnIndex === 1))) {
    let rowspan = skuOptions[1].value_ids.length; // 第2个规格的规格值个数（有2个规格，为第1列合并行数，第2列在前面已返回不合并）
    if (skuLen === 3) {
      // 有 3 个规格，第1/2列计算合并行，第3列在前面已返回不合并
      rowspan = columnIndex === 0
        ? rowspan * skuOptions[2].value_ids.length // 第1列合并行数为：第2个规格的规格值个数 * 第3个规格的规格值个数
        : skuOptions[2].value_ids.length; // 第2列合并行数为：第3个规格的规格值个数
    }

    // 合并行
    if (rowIndex % rowspan === 0) {
      return {
        rowspan,
        colspan: 1
      };
    }

    return {
      rowspan: 0,
      colspan: 0
    };
  }

  // 其它列不需要合并行
  return {
    rowspan: 1,
    colspan: 1
  };
};

const skuColumnTitle = (skuIndex) => {
  const skuOption = skuData.value.options[skuIndex];
  if (!skuOption) {
    return '';
  }
  const nameId = skuOption.name_id;
  return skuMapData.nameTitleMap[nameId] || nameId;
};

const cleanItemsMap = () => {
  const skuKeys = skuRenderData.skuList.map(item => item.skuKey);
  for (const skuKey in skuData.value.skuMap) {
    if (!skuKeys.includes(skuKey)) {
      delete skuData.value.skuMap[skuKey];
    }
  }
};

const skuDetailRow = (sku, skuIndex, valueId, baseRow = null) => {
  if (!valueId) {
    return {};
  }

  const row = {
    ['nameId' + skuIndex]: sku.name_id,
    ['valueId' + skuIndex]: valueId,
    ['valueLabel' + skuIndex]: skuMapData.valueTitleMaps[valueId] || valueId
  };

  if (baseRow) {
    // 添加 key 并重新排序，保证规格重新排序后数据能正常
    row.skuKey = `${baseRow.skuKey},${valueId}`
      .split(',')
      .sort((a, b) => a - b) // 从小到大排序
      .join(',');

    return {
      ...baseRow,
      ...row
    };
  }

  row.skuKey = valueId + '';
  return row;
};

const skuDetailRows = (skuOption, skuIndex, rows, baseRow) => {
  const skuDataLen = skuData.value.options.length;
  skuOption.value_ids.forEach((valueId) => {
    const row = skuDetailRow(skuOption, skuIndex, valueId, baseRow);
    rows.push(row);
    if (skuDataLen === skuIndex + 1) {
      skuData.value.skuMap[row.skuKey] ??= {};
    }
  });
};

// 更新明细表数据
const updateSkusData = () => {
  let skuList = [];
  skuData.value.options.forEach((skuOption, skuIndex) => {
    if (skuIndex === 0) {
      // 第1个规格选项
      skuDetailRows(skuOption, skuIndex, skuList);
      return;
    }
    // 第2、3个规格选项
    const rows = [];
    skuList.forEach(baseRow => skuDetailRows(skuOption, skuIndex, rows, baseRow));
    skuList = rows;
  });
  skuRenderData.skuList = skuList;
  cleanItemsMap();
};

// 是否是组件内更改 skuData.value.options
// 如果不是则同步 skuData.value.options 到 rawSkuOptions.value
let isInnerUpdate = false;
// 外部改变 modelValue 后，同步到 rawSkuOptions，并更新规格明细表
watch(() => skuData.value.options, () => {
  if (isInnerUpdate) {
    isInnerUpdate = false;
  } else {
    rawSkuOptions.value = skuData.value.options;
  }
  updateSkusData();
});

// 规格价格变化后，同步到商品价格
watch(() => skuData.value.skuMap, skuMap => {
  let minPrice = 0;
  for (const skuItem of Object.values(skuMap)) {
    if (!skuItem.price) {
      continue;
    }
    if (minPrice === 0 || minPrice > skuItem.price) {
      minPrice = skuItem.price;
    }
  }
  price.value = minPrice;
}, {deep: true});

// 编辑规格有效后，同步到规格（skuData）
watch(() => rawSkuOptions.value, () => {
  const effectiveOptions = []; // 有效规格
  // 遍历原始规格获取有效规格
  rawSkuOptions.value.forEach(rawSkuOption => {
    if (!rawSkuOption.name_id) {
      // 未设置规格名，规格无效
      return;
    }

    const effectiveValues = []; // 有效规格值
    rawSkuOption.value_ids.forEach(valueId => {
      if (!valueId) {
        // 未设置规格值，规格值无效
        return;
      }
      effectiveValues.push(valueId);
    });

    if (effectiveValues === [null] || effectiveValues.length === 0) {
      // 未设置规任何格值，规格无效
      return;
    }

    // 深度克隆规格
    const effectiveSku = refKit.cloneDeep({}, rawSkuOption);
    effectiveSku.value_ids = effectiveValues;
    effectiveOptions.push(effectiveSku);
  });

  // if (JSON.stringify(skuData.value.options) === JSON.stringify(effectiveOptions)) {
  //     return;
  // }

  isInnerUpdate = true;
  skuData.value.options = effectiveOptions;
}, {deep: true});

onMounted(async() => {
  if (skuData.value.options.length > 0) {
    // 加载规格完成后更新规格视图
    (async() => {
      for (const skuOption of skuData.value.options) {
        await fetchSkuValueItems(skuOption.name_id);
      }
    })().then(() => {
      refKit.cloneDeep(rawSkuOptions, skuData.value.options); // 深拷贝断绝对象传引用
      updateSkusData();
    });
  }
});
window.skuData = skuData;
</script>

<style lang="scss">
.goods-skus {
  .sku-rm {
    position: absolute;
    right: -8px;
    top: -7px;
    color: #999;
    z-index: 9;
    opacity: 0;
    font-size: 16px;

    &:after {
      display: none !important;
    }

    &:hover {
      color: var(--el-color-danger)
    }
  }

  .sku-item:hover > .sku-rm, .sku-value-item:hover .sku-rm {
    opacity: 1;
  }
}

.sku-detail-wp {
  .el-table {
    td.el-table__cell {
      padding: 2px 0;
    }

    th.el-table__cell {
      padding: 5px 0;
    }
  }

  .el-form-item__error {
    display: none;
  }
}

.sku-value-image-wp {
  margin-top: 3px;
}
</style>
