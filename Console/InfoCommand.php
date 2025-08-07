<?php

namespace MatusStafura\SystemInfo\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MatusStafura\SystemInfo\Helper\Info;
use Symfony\Component\Console\Helper\Table;

class InfoCommand extends Command
{
    protected Info $info;

    public function __construct(Info $info)
    {
        $this->info = $info;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('devtools:info')
            ->setDescription('Show system and Magento info for developers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $info = $this->info->getSystemInfoArray(); // We'll update the helper to return array instead of formatted string

        $table = new Table($output);
        $table->setHeaders(['Key', 'Value']);

        foreach ($info as $key => $value) {
            // Trim long PHP extension lists or OS info
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            if (strlen($value) > 120) {
                $value = wordwrap($value, 100, "\n", true);
            }
            $table->addRow([$key, $value]);
        }

        $table->render();
        return Command::SUCCESS;
    }
}
