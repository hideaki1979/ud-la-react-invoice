<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    products: {type:[Object, Array]},
    search_str: String,
    flash: Object,  // successMessageの代わりにflashオブジェクトを受け取る
});

const form = useForm({
    id: '',
    search_str: props.search_str || '',
});


const items = computed(() => {
    return Array.isArray(props.products) ? props.products : (props.products?.data ?? []);
});

const links = computed(() => {
    return Array.isArray(props.products) ? [] : (props.products?.links ?? []);
});

const deleteProduct = (id, name) => {
    if(confirm(name + "を削除しても良いですか？")) {
        form.delete(route('products.destroy', id));
    }
};

const search_go = () => {
    form.get(route('products.index'));
};

const flash = computed(() => props.flash);

// products が配列またはページネーターかで形が変わるため、
// 安全にアイテム数を出力する
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-normal">
                Products
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">商品一覧</div>
                </div>
            </div>

            <div class="m-3 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                    <div class="mt-4 mb-4 ml-4 flex gap-2">
                        <Link
                            :href="route('products.create')"
                            :class="'px-4 py-2 bg-indigo-500 text-white border rounded-md text-xs'"
                            >
                            <i class="fa-solid fa-plus-circle"></i>
                            商品登録
                        </Link>

                        <div>
                            <TextInput
                                id="search_str"
                                type="text"
                                class="block w-full"
                                v-model="form.search_str"
                                autocomplete="search_str"
                                @blur="search_go"
                                @keyup.enter="search_go"
                            />
                        </div>
                        <span v-if="items.length ===0 && props.search_str" class="m-2">
                            該当する商品はありません。
                        </span>
                    </div>

                    <div v-if="flash?.success" class="m-2 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ flash.success }}
                    </div>

                    <table class="table-auto border border-gray-400 w-10/12 m-4">

                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 w-12">ID</th>
                                <th class="px-4 py-2 w-48">商品</th>
                                <th class="px-4 py-2 w-28">コード</th>
                                <th class="px-4 py-2 w-28 text-center">価格</th>
                                <th class="px-4 py-2 w-28 text-center">税率</th>
                                <th class="px-4 py-2"></th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="items.length === 0">
                                <td colspan="7" class="border border-gray-400 px-4 py-2 text-center text-gray-500">
                                    商品がありません。
                                </td>
                            </tr>
                            <tr v-for="product in items" :key="product.id">
                                <td class="border border-gray-400 px-4 py-2">{{ product.id }}</td>
                                <td class="border border-gray-400 px-4 py-2">{{ product.name }}</td>
                                <td class="border border-gray-400 px-4 py-2 text-center">{{ product.code }}</td>
                                <td class="border border-gray-400 px-4 py-2 text-right">{{ product.price }}</td>
                                <td class="border border-gray-400 px-4 py-2 text-right">{{ product.tax }}</td>
                                <td class="border border-gray-400 px-4 py-2 text-center">
                                    <Link :href="route('products.edit', product.id)"
                                    :class="'px-4 py-2 bg-yellow-500 text-white border rounded-md text-xs'">
                                    <i class="fa-solid fa-edit"></i>
                                    </Link>
                                </td>
                                <td class="border border-gray-400 px-4 py-2 text-center">
                                    <DangerButton @click="deleteProduct(product.id, product.name)">
                                        <i class="fa-solid fa-trash"></i>
                                    </DangerButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="p-4 flex justify-center">
                        <nav class="inline-flex -space-x-px" aria-label="Pagination">
                            <template v-for="(link, idx) in orders.links" :key="idx">
                                <Link v-if="link.url" :href="link.url" class="px-4 py-2 border bg-white text-gray-700 text-sm hover:bg-gray-100" v-html="link.label" />
                                <span v-else class="px-4 py-2 border bg-gray-100 text-sm text-gray-500" v-html="link.label"></span>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
