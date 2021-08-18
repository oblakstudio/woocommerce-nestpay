import { Router } from 'body-class-router';
import NestPayOrders from './routes/np-orders.resolver';
import NestPaySettings from './routes/np-settings.resolver';

const routes = new Router({
  nestpaySettings: new NestPaySettings(),
  postTypeShopOrder: new NestPayOrders(),
});

jQuery(() => {
  routes.loadEvents();
});
