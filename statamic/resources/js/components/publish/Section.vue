<template>

    <div class="card p-0">
        <div class="card-body">

            <publish-fields
                :fields="fields"
                :data.sync="data"
                :errors="errors"
                :hidden-fields="hiddenFields"
                :autofocus="autofocus"
            ></publish-fields>

        </div>
    </div>

</template>


<script>
export default {

    components: {
        PublishFields: require('./Fields.vue')
    },

    props: {
        section: Object,
        fieldset: Object,
        data: Object,
        errors: Object,
        hiddenFields: Array,
        autofocus: Boolean
    },

    computed: {

        handle() {
            return this.section.handle;
        },

        fields() {
            return this.section.fields;
        }

    }

}
</script>
