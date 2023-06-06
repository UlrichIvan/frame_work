<?php

namespace App\Http;

/**
 * Class Response to send response from entry request
 */
class Response
{
      private int $status;
      private string $statusText;

      public function setStatus(int $status = null): self
      {
            $this->status = $status;
            return $this;
      }

      public function getStatus(): ?int
      {
            return $this->status;
      }

      public function setStatusText(int $statusText = null): self
      {
            $this->statusText = $statusText;
            return $this;
      }

      public function getStatusText(): ?string
      {
            return $this->statusText;
      }

      public function json(mixed $data): void
      {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
      }
}
