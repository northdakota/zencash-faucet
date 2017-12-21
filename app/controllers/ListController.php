<?php

use Hexim\HeximZcashBundle\Zcash\ZcashWrapper;
use Phalcon\Mvc\View;

/**
 * Class IndexController
 */
class ListController extends ControllerBase
{
    public function indexAction()
    {

        $wallet = new ZcashWrapper([
            'rpc_address'  => $this->config->faucet->server_addr,
            'rpc_user'     => $this->config->faucet->server_user,
            'rpc_password' => $this->config->faucet->server_passwd,
        ]);

        $command = [
            'jsonrpc' => '1.0',
            'id'      => 'curl',
            'method'  => 'z_listreceivedbyaddress',
            'params'  => [
                $this->config->faucet->z_addr,
            ],
        ];

        $result = $wallet->rpcZcashCommand($command);

        $this->view->setRenderLevel(
            View::LEVEL_LAYOUT
        );

        $outputData = [];

        $dbData = User::find([
            'columns' => 'address',
        ]);

        $dbDataArray = [];

        foreach ($dbData as $dbRow) {
            $dbDataArray[] = $dbRow['address'];
        }

        $fromArray = [];

        foreach ($result['result'] as $item) {
            $data = json_decode(trim(hex2bin($item['memo'])));
            $from = isset($data->zenmsg->from) ? $data->zenmsg->from : 'N/A';

            if (isset($data->zenmsg->from)) {
                $fromArray[] = $from;
            }

            if ($this->request->get('debug')) {
                $outputData[] = [
                    'from'    => $from,
                    'message' => $data->zenmsg->message,
                ];
            }

            // skip identity messages
            if (strpos($data->zenmsg->message, '{') === 0) {
                continue;
            }

            $messages = [];

            if (in_array($data->zenmsg->message, $dbDataArray) || in_array($from, $dbDataArray)) {
                if (array_search($data->zenmsg->message, $messages) !== false) {
                    continue;
                }
                $messages[] = $data->zenmsg->message;

                $outputData[$from] = [
                    'from'    => $from,
                    'message' => $data->zenmsg->message,
                ];
            }
        }

        $count = array_count_values($fromArray);

        $this->view->count = $count;
        $this->view->result = $outputData;

    }
}

