<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    options: { type: Array, required: true },
    modelValue: { default: '' },
    placeholder: { type: String, default: '入力して検索...' },
});

const emit = defineEmits(['update:modelValue']);

const searchText = ref('');
const isOpen = ref(false);

// 選択済みの場合、表示名をセット
watch(() => props.modelValue, (newVal) => {
    const found = props.options.find(op => op.id === newVal);
    searchText.value = found ? found.name : '';
}, { immediate: true });

// 入力テキストで絞り込み
const filteredOptions = computed(() => {
    if (!searchText.value) return props.options;
    const term = searchText.value.toLowerCase();
    return props.options.filter(op =>
        op.name.toLowerCase().includes(term) ||
        (op.code && op.code.toLowerCase().includes(term))
    );
});

const onInput = () => {
    isOpen.value = true;
    emit('update:modelValue', '');
};

const selectOption = (option) => {
    searchText.value = option.name;
    emit('update:modelValue', option.id);
    isOpen.value = false;
};

const onBlur = () => {
    setTimeout(() => {
        isOpen.value = false;

        // 選択肢が確定していない場合、入力内容をリセットする
        const selected = props.options.find(op => op.id === props.modelValue);
        if (!selected) {
            searchText.value = '';
        } else {
            // 確定しているが、表示テキストが異なる場合はもとに戻す
            searchText.value = selected.name;
        }
    }, 200);
};
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
            v-if="isOpen && filteredOptions.length > 0"
            class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
        >
            <li
                v-for="op in filteredOptions"
                :key="op.id"
                @mousedown.prevent="selectOption(op)"
                class="px-3 py-2 cursor-pointer hover:bg-indigo-50 text-sm flex justify-between"
            >
                <span>{{ op.name }}</span>
                <span class="text-gray-400">{{ op.code }}</span>
            </li>
        </ul>
        <p
            v-if="isOpen && searchText && filteredOptions.length === 0"
            class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg px-3 py-2 text-sm text-gray-500"
        >
            該当する商品がありません
        </p>
    </div>
</template>
