/* eslint-disable @typescript-eslint/no-empty-function */
import { RouteInterface } from '@wptoolset/router';
import 'magnific-popup';

export default class NestPayOrders implements RouteInterface {
  private preloader: HTMLDivElement;
  private simpleBtns: NodeListOf<HTMLAnchorElement>;
  private promptBtns: NodeListOf<HTMLAnchorElement>;

  public init(): void {
    this.preloader = <HTMLDivElement>document.getElementById('nestpay-loading');
    this.simpleBtns = document.querySelectorAll('.wc-action-button-nestpay_query, .wc-action-button-nestpay_void');
    this.promptBtns = document.querySelectorAll('.wc-action-button-nestpay_capture, .wc-action-button-nestpay_refund');
  }

  public finalize(): void {
    this.simpleBtns.forEach((button) => button.addEventListener('click', (evt) => this.handleSimpleAction(evt)));
    this.promptBtns.forEach((button) => button.addEventListener('click', (evt) => this.handlePromptAction(evt)));
  }

  private handleSimpleAction(evt: Event): void {
    evt.preventDefault();

    this.preloader.classList.add('active');

    this.sendRequest((evt.currentTarget as HTMLAnchorElement).href);
  }

  private handlePromptAction(evt: Event): void {
    evt.preventDefault();

    const prompted = prompt(window.nestpay.prompt, '');
    const amount = prompted == '' ? 0 : parseInt(prompted);
    const capture = `${(evt.currentTarget as HTMLAnchorElement).href}&amount=${amount}`;

    this.preloader.classList.add('active');

    this.sendRequest(capture);
  }

  private sendRequest(href: string): void {
    fetch(href)
      .then((response) => response.json())
      .then((response) => this.handleResponse(response));
  }

  private handleResponse(response: AjaxResponse): void {
    const html = `<div class="np-popup">
        <h3>${response.message}</h3>
        <span class="info">${window.nestpay.status}: </span>${response.data.status}<br>
        <span class="info">${window.nestpay.transCode}: </span>${response.data.code}<br>
        <span class="info">${window.nestpay.response}: </span>${response.data.response}<br>
        <span class="info">${window.nestpay.orderID}: </span>${response.data.orderID}<br>
        <span class="info">${window.nestpay.transID}: </span>${response.data.transID}<br>
        <span class="info">${window.nestpay.date}: </span>${response.data.date}<br>
      </div>`;

    this.preloader.classList.remove('active');

    jQuery.magnificPopup.open({
      items: {
        src: html,
        type: 'inline',
      },
    });
  }
}
