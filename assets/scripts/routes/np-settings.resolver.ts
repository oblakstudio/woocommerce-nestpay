import { RouteInterface } from '@wptoolset/router';

export default class NestPaySettings implements RouteInterface {
  private fields: string[];

  private switch: HTMLInputElement;

  public init(): void {
    this.fields = ['merchant_id', 'username', 'password', 'payment_url', 'api_url', 'store_key'];

    this.switch = <HTMLInputElement>document.getElementById('woocommerce_nestpay_testmode');
  }

  public finalize(): void {
    this.switch.addEventListener('change', (event) => this.maybeHideFields(event));
    this.toggleFields(this.switch.checked);

    jQuery('.select2').select2();
  }

  private maybeHideFields(e: Event): void {
    this.toggleFields((e.currentTarget as HTMLInputElement).checked);
  }

  private toggleFields(sandboxed: boolean): void {
    const shown = sandboxed ? 'test_' : '';
    const hidden = sandboxed ? '' : 'test_';

    this.fields.forEach((fieldName) => {
      document.getElementById(`woocommerce_nestpay_${shown}${fieldName}`).closest('tr').style.display = 'block';
      document.getElementById(`woocommerce_nestpay_${hidden}${fieldName}`).closest('tr').style.display = 'none';
    });
  }
}
