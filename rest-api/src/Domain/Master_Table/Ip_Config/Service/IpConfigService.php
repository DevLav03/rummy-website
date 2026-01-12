<?php

namespace App\Domain\Master_Table\Ip_Config\Service;

//Data
use App\Domain\Master_Table\Ip_Config\Data\IpConfigData;
use App\Domain\Master_Table\Ip_Config\Data\IpConfigDataRead;

//Validator
use App\Domain\Master_Table\Ip_Config\Validator\IpConfigUpdateValidator;

//Repository
use App\Domain\Master_Table\Ip_Config\Repository\IpConfigRepository;

use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;


final class IpConfigService
{
    private IpConfigRepository $repository;
    private IpConfigUpdateValidator $updateValidator;

    private LoggerInterface $logger;

    public function __construct(IpConfigRepository $repository, LoggerFactory $loggerFactory, IpConfigUpdateValidator $updateValidator)
    {
        $this->repository = $repository;
        $this->updateValidator = $updateValidator;

        $this->logger = $loggerFactory->addFileHandler('Master_Table/Ip_Config/ip_config.log')->createLogger();
    }

    //Get All Data
    public function getIpConfig(): IpConfigData
    {
        $ip = $this->repository->getIpConfig();

        $result = new IpConfigData();
     
        foreach ($ip as $ipRow) {
            $ip = new IpConfigDataRead();
            // $ip->id = $ipRow['id'];
            $ip->game_ip_address = $ipRow['game_ip_address'];
            $ip->game_port_number = $ipRow['game_port_number'];
            $ip->game_domain_name  = $ipRow['game_domain_name'];
            $ip->tourney_ip_address  = $ipRow['tourney_ip_address'];
            $ip->tourney_port_number = $ipRow['tourney_port_number'];
            $ip->tourney_domain_name  = $ipRow['tourney_domain_name'];
           


            $result->ip_config[] = $ip;
        }

        return $result;
       
    }

    //Update Data
    public function updateIpConfig(array $data): int 
    { 
        $this->updateValidator->validateUpdateData($data);

        $ip = $this->repository->updateIpConfig($data); 

        $this->logger->info(sprintf('Updated successfully: %s', $data));

        return $ip;
    }

}
