<?php

/** @var yii\web\View $this */

$this->title = 'Модуль переводчиков';
?>
<div class="translator-index">
    <div id="translator-app" class="card p-4">
        <h1 class="h3 mb-3">Подбор переводчиков</h1>
        <p class="text-muted">
            Выберите тип занятости, чтобы показать свободных исполнителей из БД.
        </p>

        <div class="mb-3">
            <label class="form-label" for="typeSelect">Режим работы</label>
            <select id="typeSelect" class="form-select" v-model="selectedType" @change="loadTranslators">
                <option value="weekday">Будние дни</option>
                <option value="weekend">Выходные дни</option>
            </select>
        </div>

        <div v-if="loading" class="alert alert-info">Загрузка переводчиков...</div>
        <div v-else-if="translators.length === 0" class="alert alert-warning">
            Нет свободных переводчиков
        </div>

        <table v-else class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Языковая пара</th>
                <th>Тип занятости</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="translator in translators" :key="translator.id">
                <td>{{ translator.id }}</td>
                <td>{{ translator.name }}</td>
                <td>{{ translator.language_pair }}</td>
                <td>{{ translator.employment_type === 'weekday' ? 'Будни' : 'Выходные' }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            selectedType: 'weekday',
            loading: false,
            translators: [],
        };
    },
    mounted() {
        this.loadTranslators();
    },
    methods: {
        async loadTranslators() {
            this.loading = true;
            try {
                const response = await fetch(`/translator/list-json?type=${this.selectedType}`);
                const payload = await response.json();
                this.translators = payload.items || [];
            } catch (error) {
                this.translators = [];
            } finally {
                this.loading = false;
            }
        },
    },
}).mount('#translator-app');
</script>
