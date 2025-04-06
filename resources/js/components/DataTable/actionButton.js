const getViewButton = (canView) => {
  if (canView) {
    return `
      <a title="View" class="view flex items-center mr-1 text-sm" href="javascript:;">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
      </a>
    `
  }
  return ''
}

const getEditButton = (canUpdate) => {
  if (canUpdate) {
    return `
      <a title="Edit" class="edit flex items-center mr-1" href="javascript:;">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
      </a>
    `
  }
  return ''
}

const getReviseButton = (canRevise) => {
  if (canRevise) {
    return `
      <a title="Revise" class="revise flex items-center mr-1" href="javascript:;">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"></path></svg>
      </a>
    `
  }
  return ''
}

const getDeleteButton = (canDelete) => {
  if (canDelete) {
    return `
      <a title="Delete" class="delete flex items-center text-theme-6 mr-1" href="javascript:;">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
      </a>
    `
  }
  return ''
}

const getSchemaButton = (canSchema) => {
  if(canSchema){
    return `
      <a title="Schema" class="schema flex items-center mr-1" href="javascript:;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
      </a>
    `
  }
  return '';
}

const getActionButtons = (canView = true, canUpdate = true, canDelete = true, canRevise = false,canSchema = false) => {
  if (!canView && !canUpdate && !canDelete) return ''

  return `
    <div class="flex justify-center">
      ${ getViewButton(canView) }
      ${ getEditButton(canUpdate) }
      ${ getReviseButton(canRevise) }
      ${ getSchemaButton(canSchema) }
      ${ getDeleteButton(canDelete) }
    </div>
  `
}

export {
  getViewButton,
  getEditButton,
  getDeleteButton,
  getActionButtons,
  getSchemaButton
}