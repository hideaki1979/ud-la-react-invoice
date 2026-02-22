<script setup>
import axios from 'axios';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    modelValue: { default: '' },
    placeholder: { type: String, default: '入力して検索...' },
    searchUrl: {
        type: String,
        required: true,
    },
    // 初期表示名（編集画面で選択済みの商品名を表示するため）
    initialDisplayName: {type: String, default: ''},
});

const emit = defineEmits(['update:modelValue', 'selected']);

const searchText = ref(props.initialDisplayName);
const isOpen = ref(false);
const options = ref([]);
const hasSearched = ref(false);

// デバウンス用タイマー
let debounceTimer = null;

// 検索クエリの変更を監視し、デバウンスしてAPIを呼び出す
const onInput = () => {
    isOpen.value = true;
    hasSearched.value = true;
    emit('update:modelValue', '');

    // デバウンスしてAPI取得
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(async () => {
        if (searchText.value.length > 0) {
            try {
                const response = await axios.get(props.searchUrl, {
                    params: { query: searchText.value },
                });
                options.value = response.data;
            } catch (error) {
                console.error('Search failed:', error);
                options.value = [];
            }
        } else {
            options.value = [];
        }
    }, 300);
};

const selectOption = (option) => {
    searchText.value = option.name;
    emit('update:modelValue', option.id);
    emit('selected', option);
    isOpen.value = false;
    hasSearched.value = false;
};

const onBlur = () => {
    setTimeout(() => {
        isOpen.value = false;

        // 選択肢が確定していない場合、入力内容をリセットする
        if (!props.modelValue) {
            searchText.value = '';
        }
    }, 200);
};

// 外部からmodelValueが変わった場合（初期値セットなど）
watch(() => props.initialDisplayName, (newVal) => {
    if (!isOpen.value) {
        searchText.value = newVal;
    }
});

</script>

<template>
    <div class="relative">
        <input
            type="text"
            v-model="searchText"
            @input="onInput"
            @focus="isOpen = true"
            @blur="onBlur"
            :placeholder="placeholder"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
        />
        <ul
            v-if="isOpen && options.length > 0"
            class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
        >
            <li
                v-for="op in options"
                :key="op.id"
                @mousedown.prevent="selectOption(op)"
                class="px-3 py-2 cursor-pointer hover:bg-indigo-50 text-sm flex justify-between"
            >
                <span>{{ op.name }}</span>
                <span class="text-gray-400">{{ op.code }}</span>
            </li>
        </ul>
        <p
            v-if="isOpen && searchText && options.length === 0 && hasSearched"
            class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg px-3 py-2 text-sm text-gray-500"
        >
            該当する項目がありません
        </p>
    </div>
</template>
