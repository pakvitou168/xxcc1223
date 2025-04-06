export default {
    data() {
        return {
            authorizedFunctions: JSON.parse(document.querySelector('#authorized-functions')?.value ?? '[]'),
        }
    },
    computed: {
        // Filter menus that has permission to show
        authorizedMenuItems() {
            return this.menuItems.filter((menuItem) => {
            // If has code, check permission
            if (menuItem.subItems) {
                let authorizedSubItems = menuItem.subItems.filter((subItem) => {
                    if (subItem.code) {
                        return this.authorizedFunctions.some(item => item.code === `${subItem.code+'.VIEW'}`);
                    }
                    // If has no code, always return true
                    return true
                })
    
                menuItem.subItems = authorizedSubItems
                return authorizedSubItems.length !== 0
            }
            else {
                // If has code, check permission
                if (menuItem.code) {
                    return this.authorizedFunctions.some(item => item.code === `${menuItem.code+'.VIEW'}`);
                }
                // If has no code, always return true
                return true
            }
            });
        },
    }
}