import moment from "moment";

export const transformClaimRegisterForDisplay = (response) => {
  let res = {...response};

  if (res.data) {
    res.data.date_of_loss = formatDate(res.data.date_of_loss);
    res.data.notification_date = formatDate(res.data.notification_date);
    res.data.date_of_disability = formatDate(res.data.date_of_disability);
    res.data.date_of_completed_doc = formatDate(res.data.date_of_completed_doc);
  }

  return res;
};

export const transformSchemaForDisplay = (responseData) => {
  let res = {...responseData};

  if (res.data?.claim) {
    res.data.claim.date_of_loss = moment(res.data.claim.date_of_loss).format("DD-MMM-YYYY");
    res.data.claim.notification_date = moment(res.data.claim.notification_date).format("DD-MMM-YYYY");
    res.data.claim.date_of_disability = moment(res.data.claim.date_of_disability).format("DD-MMM-YYYY");
    res.data.claim.date_of_completed_doc = moment(res.data.claim.date_of_completed_doc).format("DD-MMM-YYYY");
  }

  if(res.data?.schema_data && res.data?.schema_data.length > 0) {
    res.data.schema_data = res.data.schema_data.map((item, index) => {

      item.admission_date = moment(item.admission_date).format("DD-MMM-YYYY");
      item.discharge_date = moment(item.discharge_date).format("DD-MMM-YYYY");

      return item;
    })
  }

  return res;
};

export const prepareSchemaPayload = (schemaData) => {
  let data = {...schemaData};

  data.date_of_loss = formatDate(data.date_of_loss, "DD-MMM-YYYY", "YYYY-MM-DD");
  data.notification_date = formatDate(data.notification_date, "DD-MMM-YYYY", "YYYY-MM-DD");
  data.date_of_disability = formatDate(data.date_of_disability, "DD-MMM-YYYY", "YYYY-MM-DD");
  data.date_of_completed_doc = formatDate(data.date_of_completed_doc, "DD-MMM-YYYY", "YYYY-MM-DD");

  if(data.schema_data && data.schema_data.length > 0) {
    data.schema_data = data.schema_data.map((item, index) => {

      item.admission_date = moment(item.admission_date).format("YYYY-MM-DD");
      item.discharge_date = moment(item.discharge_date).format("YYYY-MM-DD");

      return item;
    })
  }

  return data;
};

export const formatDate = (inputDate, inputFormat = "YYYY-MM-DD", outputFormat = "DD-MMM-YYYY") => {

  if(isValidISODate(inputDate) ) {
    return moment(inputDate).format("YYYY-MM-DD");
  }

  if(isValidDDMMMYYYYDate(inputDate) ) {
    return moment(inputDate, "DD/MMM/YYYY").format("YYYY-MM-DD");
  }

  if (inputDate && moment(inputDate, inputFormat, true).isValid()) {
    return moment(inputDate, inputFormat).format(outputFormat);
  }

  return inputDate;
};

export const isValidISODate = (dateString) => {

  return moment(dateString, moment.ISO_8601, true).isValid();
}

export const isValidDDMMMYYYYDate = (dateString) => {

  return moment(dateString, "DD/MMM/YYYY", true).isValid();
}