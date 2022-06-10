import { WpRouter } from '@wptoolset/router';
// import NestPayOrders from './routes/np-orders.resolver';
import NestPaySettings from './routes/np-settings.resolver';

const routes = new WpRouter({
  nestpaySettings: () => new NestPaySettings(),
  //   postTypeShopOrder: () => new NestPayOrders(),
});

jQuery(() => {
  routes.loadEvents();
});
