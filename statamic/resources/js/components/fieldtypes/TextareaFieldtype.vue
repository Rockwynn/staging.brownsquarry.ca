<template>
    <textarea class="form-control" v-model="data" v-el:textarea v-elastic></textarea>
</template>

<script>
export default {
    mixins: [Fieldtype]
};
</script>
