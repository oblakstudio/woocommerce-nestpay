//Magnific Popup Definition File

interface JQuery {
  magnificPopup(callback?: () => void): JQuery ;
}

interface JQueryStatic {
  magnificPopup: JQueryMagnificPopupStatic;
}

interface JQueryMagnificPopupStatic {
  open(opts?: unknown): JQuery;
  (): JQuery;
  parameter(name: string): string;
  parameter(name: string, value: string, append?: boolean): JQuery;
}
