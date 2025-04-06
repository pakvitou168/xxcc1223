// resources/js/mixins/dropdownTransform.js

export const dropdownTransform = {
    methods: {
        transformOptionsToDropdown(data) {
            if (!data || typeof data !== 'object') {
                return [];
            }

            return Object.entries(data).map(([value, label]) => ({
                value,
                label: label?.toString() || value
            }));
        }
    }
};

// Usage example in a component:
/*
import { dropdownTransform } from '@/mixins/dropdownTransform';

export default {
  mixins: [dropdownTransform],
  data() {
    return {
      options: {
        '1': 'Option 1',
        '2': 'Option 2'
      },
      dropdownOptions: []
    }
  },
  created() {
    this.dropdownOptions = this.transformOptionsToDropdown(this.options);
  }
}
*/