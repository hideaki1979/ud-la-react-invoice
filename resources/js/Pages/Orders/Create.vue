<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ComboBoxInput from '@/Components/ComboBoxInput.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';


const props = defineProps({});

const form = useForm({
    customer_id: '',
    orderday: '',
    products: [
        {id: '', quantity: '', _uid: 0},
    ],
});

const nextProductId = ref(1);

// 商品行の追加
const addProduct = () => {
    form.products.push({id: '', quantity: '', _uid: nextProductId.value});
    nextProductId.value++;
};

// 商品行の削除
const removeProduct = (index) => {
    form.products.splice(index, 1);
};

// 選択時に動的追加する
const productMap = reactive({});

const onProductSelected = (product) => {
    productMap[product.id] = product;
};

// 各行の選択された商品情報を取得
const getSelectedProduct = (productId) => {
    return productMap[productId];
};

// 合計金額（税込）
const totalAmount = computed(() => {
    return form.products.reduce((sum, item) => {
        const product = getSelectedProduct(item.id);
        if (product && item.quantity) {
            const taxRate = 1 + product.tax / 100;
            return sum + Math.floor(product.price * item.quantity * taxRate);
        }
        return sum;
    }, 0);
});

const submit = () => {
    form.post(route('orders.store'), {
        onSuccess: () => form.reset(),
    });
}

</script>

<template>
    <Head title="オーダー登録" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-normal">
                オーダー登録
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">オーダー登録</div>
                </div>

                <div class="mt-4 mb-4 ml-4 flex">
                    <Link
                        :href="route('orders.index')"
                        :class="'px-4 py-2 bg-indigo-500 text-white border rounded-md text-sm'"
                    >
                        <i class="fa-solid fa-backward"></i> 戻る
                    </Link>
                </div>

                <form @submit.prevent="submit" class="w-full">
                    <div class="mt-4">
                        <InputLabel for="customer_id" value="客先" />
                        <ComboBoxInput
                            :search-url="route('api.customers.search')"
                            id="customer_id"
                            class="mt-2 block w-80"
                            v-model="form.customer_id"
                        />
                        <InputError class="mt-2" :message="form.errors.customer_id" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="orderday" value="注文日" />
                        <TextInput
                            id="orderday"
                            type="date"
                            class="mt-2 block w-80"
                            v-model="form.orderday"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.orderday" />
                    </div>

                    <!-- 商品リスト -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-2">
                            <InputLabel value="商品明細" />
                            <button
                                type="button"
                                @click="addProduct"
                                class="px-4 py-2 bg-green-500 text-white border rounded-md text-sm hover:bg-green-600"
                            >
                                <i class="fa-solid fa-plus"></i> 商品追加
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.products" />

                        <div
                            v-for="(item, index) in form.products"
                            :key="item._uid"
                            class="mt-4 p-4 bg-gray-50 rounded-md border"
                        >
                            <div class="flex items-start gap-4 flex-wrap">
                                <div>
                                    <InputLabel :for="'product_id_' + index" value="商品" />
                                    <ComboBoxInput
                                        :search-url="route('api.products.search')"
                                        :id="'product_id_' + index"
                                        class="mt-2 block w-80"
                                        v-model="item.id"
                                        @selected="onProductSelected"
                                    />
                                    <InputError class="mt-2" :message="form.errors['products.' + index + '.id']" />
                                </div>

                                <template v-if="getSelectedProduct(item.id)">
                                    <div class="w-24">
                                        <InputLabel value="コード" />
                                        <div class="mt-4 ml-2 text-xl">
                                            {{ getSelectedProduct(item.id).code }}
                                        </div>
                                    </div>
                                    <div class="w-24">
                                        <InputLabel value="価格" />
                                        <div class="mt-4 ml-2 text-xl">
                                            {{ getSelectedProduct(item.id).price }}円
                                        </div>
                                    </div>
                                    <div class="w-24">
                                        <InputLabel value="税率" />
                                        <div class="mt-4 ml-2 text-xl">
                                            {{ getSelectedProduct(item.id).tax }}％
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel :for="'quantity_' + index" value="注文数" />
                                        <TextInput
                                            :id="'quantity_' + index"
                                            type="number"
                                            class="mt-2 block w-36"
                                            v-model="item.quantity"
                                            required
                                            min="1"
                                        />
                                        <InputError class="mt-2" :message="form.errors['products.' + index + '.quantity']" />
                                    </div>
                                </template>

                                <!-- 削除ボタン（2行目以降） -->
                                <div v-if="form.products.length > 1" class="mt-8">
                                    <button
                                        type="button"
                                        @click="removeProduct(index)"
                                        class="px-4 py-2 bg-red-500 text-white border rounded-md text-sm hover:bg-red-600"
                                    >
                                        <i class="fa-solid fa-trash"></i>削除
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 合計金額 -->
                    <div v-if="totalAmount > 0" class="mt-6 p-4 bg-blue-50 rounded-md border border-blue-200">
                        <div class="text-lg">
                            合計金額（税込）： {{ totalAmount.toLocaleString() }}円
                        </div>
                    </div>

                    <div class="flex items-center mt-8 mb-8">
                        <PrimaryButton
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            登録
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
