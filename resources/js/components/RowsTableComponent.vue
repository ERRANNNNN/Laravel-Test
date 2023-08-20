<script>
export default {
    props: {
        rows: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            tableRows: []
        }
    },
    computed: {
        // Группированные по date строки
        grouped() {
            return this.tableRows.reduce((acc, n) => {
                (acc[n.date] = acc[n.date] || []).push(n);
                return acc;
            }, {});
        },
    },
    mounted() {
        this.tableRows = this.rows;
        // Подпишемся на событие создания записи в rows
        // и в случае если ещё нет в таблице строки с таким id - отобразим
        Echo.private('rows').listen('RowsUpdated', (e) => {
            let exists = this.tableRows.findIndex((r) => {
                return r.id === e.rows.id;
            })
            if (exists === -1) {
                this.tableRows.push(e.rows);
            }
        });
    }
}
</script>

<template>
    <table class="w-3/4 leading-normal mx-auto">
        <thead>
        <tr>
            <th
                class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider" colspan="3">
                Rows
            </th>
        </tr>
        </thead>
        <tbody>
        <template v-for="dateRows in grouped">
            <template v-for="(row, index) in dateRows">
                <template v-if="index === 0">
                    <tr class="w-full">
                        <td class="border-b border-gray-200 bg-white text-sm w-full"
                            style="padding: 10px 1.5rem" colspan="3">
                            <p class="text-gray-900 whitespace-no-wrap">
                                <b>Date: {{ row.date }}</b>
                            </p>
                        </td>
                    </tr>
                </template>
                <tr>
                    <td class="border-b border-gray-200 bg-white text-sm" style="padding: 10px 1.5rem">
                        <p class="text-gray-900 whitespace-no-wrap">
                            ID: {{ row.id }}
                        </p>
                    </td>
                    <td class="border-b border-gray-200 bg-white text-sm" style="padding: 10px 1.5rem">
                        <p class="text-gray-900 whitespace-no-wrap">
                            EXCEL_ID: {{ row.excel_id }}
                        </p>
                    </td>
                    <td class="border-b border-gray-200 bg-white text-sm" style="padding: 10px 1.5rem">
                        <p class="text-gray-900 whitespace-no-wrap">
                            Name: {{ row.name }}
                        </p>
                    </td>
                </tr>
            </template>
        </template>
        </tbody>
    </table>
</template>
