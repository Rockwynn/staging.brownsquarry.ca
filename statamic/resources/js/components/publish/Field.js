const defaultLocale = Object.keys(Statamic.locales)[0];
const currentLocale = Statamic.Publish ? (Statamic.Publish.locale || defaultLocale) : defaultLocale;
const isEditingDefaultLocale = currentLocale === defaultLocale;

export default {

    props: ['field', 'data', 'config', 'autofocus'],

    computed: {

        isVisible() {
            return !this.$parent.hiddenFields.includes(this.field.name);
        },

        isReadOnly() {
            return !isEditingDefaultLocale && !this.isLocalizable;
        },

        hasError() {
            return _.has(this.$parent.errors, 'fields.'+this.field.name);
        },

        classes() {
            return [
                'form-group p-2 m-0',
                this.fieldtypeClass,
                tailwind_width_class(this.field.width),
                { 'has-error': this.hasError }
            ];
        },

        fieldtypeClass() {
            return this.field.type + '-fieldtype';
        }

    }

}
