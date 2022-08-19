<?php
/**
 * @link https://github.com/phpviet/omnipay-momo
 * @copyright (c) PHP Viet
 * @license [MIT](http://www.opensource.org/licenses/MIT)
 */

namespace Omnipay\MoMo\Message\AllInOne;

/**
 * @link https://developers.momo.vn/#/docs/aio/?id=ph%c6%b0%c6%a1ng-th%e1%bb%a9c-thanh-to%c3%a1n
 *
 * @author Vuong Minh <vuongxuongminh@gmail.com>
 * @since 1.0.0
 */
class PurchaseRequest extends AbstractSignatureRequest
{
    /**
     * {@inheritdoc}
     */
    protected $responseClass = PurchaseResponse::class;

    /**
     * {@inheritdoc}
     */
    public function initialize(array $parameters = [])
    {
        parent::initialize($parameters);
        $this->setOrderInfo($this->getParameter('orderInfo') ?? '');
        $this->setExtraData($this->getParameter('extraData') ?? '');
        $this->setParameter('requestType', 'captureMoMoWallet');

        return $this;
    }

    /**
     * Trả về extra data gửi đến MoMo.
     *
     * @return null|string
     */
    public function getExtraData(): ?string
    {
        return $this->getParameter('extraData');
    }

    /**
     * Thiết lập dữ liệu kèm theo đơn hàng.
     *
     * @param null|string $data
     * @return $this
     */
    public function setExtraData(?string $data)
    {
        return $this->setParameter('extraData', $data);
    }

    /**
     * Trả về order info gửi đến MoMo.
     *
     * @return null|string
     */
    public function getOrderInfo(): ?string
    {
        return $this->getParameter('orderInfo');
    }

    /**
     * Thiết lập thông tin đơn hàng.
     *
     * @param null|string $info
     * @return $this
     */
    public function setOrderInfo(?string $info)
    {
        return $this->setParameter('orderInfo', $info);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSignatureParameters(): array
    {
        return [
            'partnerCode', 'accessKey', 'requestId', 'amount', 'orderId', 'orderInfo', 'notifyUrl',
            'extraData', 'requestType', 'redirectUrl', 'ipnUrl',
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function sendData($data)
    {
        $response = $this->httpClient->request('POST', $this->getEndpoint() . '/v2/gateway/api/create', [
            'Content-Type' => 'application/json; charset=UTF-8',
        ], json_encode($data));
        $responseClass = $this->responseClass;
        $responseData = $response->getBody()->getContents();

        return $this->response = new $responseClass($this, json_decode($responseData, true) ?? []);
    }
}
