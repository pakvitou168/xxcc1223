const listTreeOrganizationOptions = branches => {
  return branches.map((organization) => {
    let branchNodes = organization.branches.map((branch) => {
      return getNode(`${organization?.id}-${branch?.id}`, branch?.name)
    })
    return getNode(organization?.id, organization?.name, branchNodes)
  }) 
}

const getNode = (key, label, children = []) => {
  return {
    key: key,
    label: label,
    children: children,
  }
}

const getSelectedOrganizations = (options, selected) => {
  let newSelected = [];
  Object.keys(selected).forEach((key) => {
    let splitKey = key.split('-');
    let parentId = +splitKey[0];
    let optionParent = options.find(option => option.key === parentId);

    if(splitKey.length === 1) { /** parents  */
      newSelected.push({
        id: parentId,
        name: optionParent.label,
        branches: []
      });

    } else if(splitKey.length === 2) { /** child  */
      let parentIndex = newSelected.findIndex(item => item.id === parentId);
      let optionChildren = optionParent.children.find(option => option.key === key);

      newSelected[parentIndex].branches.push({
        id: +splitKey[1],
        name: optionChildren.label,
        org_id: parentId
      });
    }
  });

  return newSelected;
}

const listTreeSelectedOrganizations = ((options, selected) => {
  let newSelected = {};

  selected.forEach(parent => {
    let children = options.find(option => option.key === parent.id).children;
    
    let hasChildren = !! children?.length;
    let isCheckedAll = parent.branches?.length == children?.length;

    newSelected[parent.id] = {
      checked: ! hasChildren || isCheckedAll,
      partialChecked: hasChildren && ! isCheckedAll
    }

    /** append of branches to select */
    parent.branches.forEach(branch => { 
      newSelected[parent.id + '-' + branch.id] = {
        checked: true,
        partialChecked: false
      }
    });
  });

  return newSelected;
});

const getExpandedKeys = (selectedBranches) => {
  let expandedKeys = {}

  Object.keys(selectedBranches).filter(item => item.split('-').length === 1).forEach(item => {
    expandedKeys[item] = true
  })

  return expandedKeys
}

export {
  listTreeOrganizationOptions,
  listTreeSelectedOrganizations,
  getSelectedOrganizations,
  getExpandedKeys,
}