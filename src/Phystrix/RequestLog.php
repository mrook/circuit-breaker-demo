<?php

namespace Demo\Phystrix;

class RequestLog extends \Odesk\Phystrix\RequestLog
{
    public function getExecutedCommandsAsString()
    {
        $output = "";
        $executedCommands = $this->getExecutedCommands();
        $aggregatedCommandsExecuted = [];
        $aggregatedCommandExecutionTime = [];

        /** @var \Odesk\Phystrix\AbstractCommand $executedCommand */
        foreach ($executedCommands as $executedCommand) {
            $display = $executedCommand->getCommandKey() . "[";
            $events = $executedCommand->getExecutionEvents();

            if (count($events) > 0) {
                foreach ($events as $event) {
                    $display .= "{$event}, ";
                }
                $display = substr($display, 0, strlen($display) - 2);
            } else {
                $display .= "Executed";
            }

            $display .= "]";

            if (!isset($aggregatedCommandsExecuted[$display])) {
                $aggregatedCommandsExecuted[$display] = 0;
            }

            $aggregatedCommandsExecuted[$display] = $aggregatedCommandsExecuted[$display] + 1;

            $executionTime = $executedCommand->getExecutionTimeInMilliseconds();

            if ($executionTime < 0) {
                $executionTime = 0;
            }

            if (isset($aggregatedCommandExecutionTime[$display]) && $executionTime > 0) {
                $aggregatedCommandExecutionTime[$display] = $aggregatedCommandExecutionTime[$display] + $executionTime;
            } else {
                $aggregatedCommandExecutionTime[$display] = $executionTime;
            }
        }

        foreach ($aggregatedCommandsExecuted as $display => $count) {
            if (strlen($output) > 0) {
                $output .= ", ";
            }

            $output .= "{$display}";

            $output .= "[" . $aggregatedCommandExecutionTime[$display] . "ms]";

            if ($count > 1) {
                $output .= "x{$count}";
            }
        }

        return $output;
    }
}
