class ControllerShopmanagerWalmart extends Controller {
    public function authorize() {
        $this->load->model('shopmanager/walmart');
        $connectionapi = $this->model_shopmanager_walmart->getApiCredentials();
        $this->model_shopmanager_walmart->redirectToAuthorizationEndpoint($connectionapi);
    }

    public function handleAuthorizationRedirect() {
        $this->load->model('shopmanager/walmart');
        $connectionapi = $this->model_shopmanager_walmart->getApiCredentials();
        $this->model_shopmanager_walmart->handleAuthorizationRedirect($connectionapi);
    }
}