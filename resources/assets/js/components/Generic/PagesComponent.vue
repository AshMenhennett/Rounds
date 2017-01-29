<template>
    <nav class="pagination-nav" aria-label="Page navigation">
        <ul class="pagination">
            <li :class="{ 'disabled' : !pagination.links.previous }">
                <a href="#" aria-label="Previous" @click.prevent="changePage(pagination.current_page - 1)">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li
                v-for="page in parseInt(pagination.total_pages)"
                class="page-item"
                :class="{ 'active' : pagination.current_page === page}"
            >
                <a href="#" @click.prevent="changePage(page)">{{ page }}</a>
            </li>
            <li :class="{ 'disabled' : !pagination.links.next }">
                <a href="#" aria-label="Next" @click.prevent="changePage(pagination.current_page + 1)">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</template>


<script>
    export default {
        data () {
            return {
                players: [],
                meta: null
            }
        },
        props: {
            pagination: {},
            for: {
                type: String,
                default: 'default'
            }
        },
        methods: {
            changePage(page) {
                if (page < 1 || page > this.pagination.total_pages) {
                    return;
                }

                this.$emit(this.for + 'ChangedPage', page);
            }
        }
    }
</script>
