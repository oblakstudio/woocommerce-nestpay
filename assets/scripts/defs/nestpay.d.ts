interface Window {
  nestpay: {
    prompt: string,
    response : string,
    transCode : string,
    transID : string,
    orderID : string,
    date : string,
    status : string,
  }
}

interface AjaxResponse {
  status: number,
  message: string,
  data: ApiResponse
}

interface ApiResponse {
  code: string,
  response: string,
  orderID: string,
  transID: string,
  status: string,
  date:string

}
