<?php

namespace App\Enums;

enum FileTypes: string
    {
        case IMAGE = 'image';
        case VIDEO = 'video';
        case DOCX = 'docx';
        case DOC = 'doc';
        case PDF = 'pdf';
        case XLS = 'xls';
        case CSV = 'csv';
    }