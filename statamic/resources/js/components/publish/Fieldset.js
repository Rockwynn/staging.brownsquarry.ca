class Fieldset {

    constructor(fieldset) {
        this.fieldset = fieldset;
        this.sections = fieldset.sections;
        this.metaFields = ['slug'];
    }

    /**
     * By default, the date field won't be shown.
     * This lets you specify whether or not it should be shown.
     */
    showDate(show) {
        if (show) this.metaFields.push('date');
        return this;
    }

    /**
     * By default, taxonomies fields won't be shown.
     * This lets you specify whether or not they should be shown.
     */
    showTaxonomies(show) {
        if (show) this.metaFields.push('taxonomies');
        return this;
    }

    /**
     * Place a title field at the beginning of the first section
     * if it hasn't been explicitly placed somewhere else.
     */
    prependTitle() {
        if (! this.fieldsInSections().includes('title')) {
            this.firstSectionFields().unshift({
                name: 'title',
                type: 'text',
                instructions: null,
                width: 100
            });
        }

        return this;
    }

    /**
     * Append any required meta fields to the end of the sidebar.
     */
    appendMeta() {
        this.ensureSidebar();

        let fields = this.fieldsInSections();

        _.each(this.metaFields, field => {
            if (field === 'taxonomies') {
                this.appendTaxonomies();
            } else {
                if (!fields.includes(field)) {
                    this.pushSidebarField({ name: field, type: field });
                }
            }
        });

        this.removeEmptySidebar();

        return this;
    }

    /**
     * Append any leftover taxonomy fields to the end of the sidebar.
     */
    appendTaxonomies() {
        const fields = this.fieldsInSections();

        this.fieldset.taxonomies.forEach(taxonomy => {
            if (fields.includes(taxonomy.handle)) return;

            this.pushSidebarField({
                type: 'taxonomy',
                name: taxonomy.handle,
                display: taxonomy.title,
            });
        });
    }

    /**
     * Push a field into the sidebar
     */
    pushSidebarField(config) {
        let sidebar = this.sidebarSectionFields();

        let field = Object.assign({
            isMeta: true,
            width: 100,
            localizable: true
        }, config || {});

        sidebar.push(field);
    }

    /**
     * Customizing the sidebar is not a requirement, but we expect one to
     * exist. If it's not already defined, we'll create a blank one here.
     */
    ensureSidebar() {
        const sidebar = _.find(this.sections, { handle: 'sidebar' });

        if (! sidebar) {
            this.sections.push({ handle: 'sidebar', display: translate('cp.meta'), fields: [] });
        }
    }

    /**
     * It's possible that all the fields that would normally be in the
     * sidebar have been placed in other sections, resulting in an
     * empty sidebar. If it's empty, we'll just get rid of it.
     */
    removeEmptySidebar() {
        if (this.sidebarSectionFields().length > 0) return;

        this.sections = _.reject(this.sections, section => section.handle == 'sidebar');
    }

    /**
     * Get the names of fields that have been placed into a section.
     */
    fieldsInSections() {
        return _.chain(this.sections).map(section => section.fields).flatten().pluck('name').value();
    }

    /**
     * Get the fields that are in the first section.
     */
    firstSectionFields() {
        return this.sections[0].fields;
    }

    /**
     * Get the fields that are in the sidebar.
     */
    sidebarSectionFields() {
        return _.find(this.sections, { handle: 'sidebar' }).fields;
    }

    /**
     * Get all the fields from all the sections.
     */
    fields() {
        return _.chain(this.sections).pluck('fields').flatten().value();
    }

}

export default Fieldset;
